<?php

class cTrade {

	const TRADE_BY_ADMIN = "A";
	const TRADE_ENTRY = "T";
	const TRADE_REVERSAL = "R";
	const TRADE_MONTHLY_FEE = "M";
	const TRADE_MONTHLY_FEE_REVERSAL = "N";

	var $trade_id;
	var $trade_date;
	var $status;
	var $member_from;
	var $member_to;
	var $amount;
	var $category;		// this will be an object of class Category
	var $description;
	var $type;
	var $feedback_buyer;	// added after trade completed; object of type cFeedback
	var $feedback_seller; // added after trade completed; object of type cFeedback

	public function __construct($member_from = NULL, $member_to = NULL, $amount=NULL, $category=NULL, $description=NULL, $type='T') {
		if ($member_from) {
			$this->status = 'V';  // Doesn't make sense for a new Trade to not be valid
			$this->amount = $amount;
			$this->description = $description;
			$this->member_from = $member_from;
			$this->member_to = $member_to;
			$this->type = $type;
			$this->category = Category::getById($category);
		}
	}

	function ShowTrade() {
		$content = $this->trade_id .", ". $this->trade_date .", ". $this->status .", ". $this->member_from->member_id .", ". $this->member_to->member_id .", ". $this->amount .", ". $this->category->id .", ". $this->description .", ". $this->type;
		return $content;
	}

	public function SaveTrade() {
		$row = array(
			"trade_date" => date("Y-m-d h:i:s"),
			"status" => $this->status,
			"member_id_from" => $this->member_from->member_id,
			"member_id_to" => $this->member_to->member_id,
			"amount" => $this->amount,
			"category" => $this->category->id,
			"description" => $this->description,
			"type" => $this->type,
		);
		$id = PDOHelper::insert(DB::TRADES, $row);
		if (!$id) {
			return FALSE;
		}
		$this->trade_id = $id;
		$this->trade_date = $row["trade_date"];
		return TRUE;
	}

	public function LoadTrade($tradeId) {
		$tableName = DB::TRADES;
		$sql = "
			SELECT date_format(trade_date, '%Y-%m-%d') AS trade_date, status, member_id_from, member_id_to, amount, description, type, category
			FROM $tableName WHERE trade_id = :id
		";
		$row = PDOHelper::fetchRow($sql, array("id" => $tradeId));
		if (empty($row)) {
			cError::getInstance()->Error("Ha ocurrido un error actualizando los datos. Intentalo otra vez mas tarde");
			return;
		}

		$this->trade_id = $tradeId;
		$this->trade_date = $row["trade_date"];
		$this->status = $row["status"];
		$this->member_from = new cMember();
		$this->member_from->LoadMember($row["member_id_from"]);
		$this->member_to = new cMember();
		$this->member_to->LoadMember($row["member_id_to"]);
		$this->amount = $row["amount"];
		$this->description = $row["description"];
		$this->type = $row["type"];
		$this->category = Category::getById($row["category"]);
/*
		$feedback = new cFeedback;
		$feedback_id = $feedback->FindTradeFeedback($tradeId, $this->member_from->member_id);
		if ($feedback_id) {
			$this->feedback_buyer = new cFeedback;
			$this->feedback_buyer->LoadFeedback($feedback_id);
		}
		$feedback_id = $feedback->FindTradeFeedback($tradeId, $this->member_to->member_id);
		if ($feedback_id) {
			$this->feedback_seller = new cFeedback;
			$this->feedback_seller->LoadFeedback($feedback_id);
		}
*/
	}

	// It is very important that this function prevent the database from going out balance.
	function MakeTrade($reversed_trade_id=NULL) {
		if ($this->amount <= 0 and $this->type != TRADE_REVERSAL) // Amount should be positive unless
			return false;									 // this is a reversal of a previous trade.

		if ($this->amount >= 0 and $this->type == TRADE_REVERSAL)	 // And likewise.
			return false;

		if ($this->member_from->member_id == $this->member_to->member_id)
			return false;		// don't allow trade to self

		if ($this->member_from->restriction==1) { // This member's account has been restricted - he is not allowed to make outgoing trades

			return false;
		}

		$balances = new cBalancesTotal;

		// TODO: At some point, we should handle out-of-balance problems without shutting
		// down all trades.  But for now, seems like a wonderfully simple solution.
		//
		// [chris] Have added a few more methods for dealing with out-of-balance scenarios (admin can set his/her preferred method in inc.config.php)

		if(!$balances->Balanced()) {

			if (OOB_EMAIL_ADMIN==true) // Admin wishes to receive an email notifying him/her when db is found to be out-of-balance
				$mailed = mail(EMAIL_ADMIN, "Database out of balance on ".SITE_LONG_TITLE."!", "Hi admin,\n\nWe thought you should know that whilst processing a trade the system detected that your trade database is out of balance! Obviously something has gone wrong somewhere along the line and we suggest you investigate the cause of this ASAP.\n\nhttp://".SERVER_DOMAIN.SERVER_PATH_URL."", EMAIL_FROM);

			switch(OOB_ACTION) { // How should we handle the out-of-balance event?

				case("FATAL"): // FATAL: The original method for dealing which is to abort the transaction

					cError::getInstance()->Error("The trade database is out of balance!  Please contact your administrator at ". PHONE_ADMIN .".", ERROR_SEVERITY_HIGH);
					return FALSE;

				break;

				default: // SILENT: Just ignore the situation and don't burden the user with warnings/error messages

						// doing nothing...

				break;
			}
		}

		// NOTE: Need table type InnoDB to do the following transaction-style statements.
		PDOHelper::set("AUTOCOMMIT", 0);
		$pdo = DB::getPDO();
		$pdo->beginTransaction();

		if (!$this->SaveTrade()) {
			PDOHelper::set("AUTOCOMMIT", 1);
			return false;
		}

		$success1 = $this->member_from->UpdateBalance(-($this->amount));
		$success2 = $this->member_to->UpdateBalance($this->amount);

		if (LOG_LEVEL > 0 and $this->type != TRADE_ENTRY) {//Log if enabled & not an ordinary trade
			$log_entry = new cLogEntry (TRADE, $this->type, $this->trade_id);
			$success3 = $log_entry->SaveLogEntry();
		} else {
			$success3 = true;
		}

		if ($reversed_trade_id) {  // If this is a trade reversal, need to mark old trade reversed
			$success4 = PDOHelper::update(DB::TRADES, array("status" => "R"), "trade_id = :id", array("id" => $reversed_trade_id));
		} else {
			$success4 = true;
		}

		if ($success1 and $success2 and $success3 and $success4) {
			$pdo->commit();
			PDOHelper::set("AUTOCOMMIT", 1);
			return true;
		} else {
			$pdo->rollBack();
			PDOHelper::set("AUTOCOMMIT", 1);
			return false;
		}
	}

	function ReverseTrade($description) {
		$user = cMember::getCurrent();

		if ($this->status == "R")
			return false;		// Can't reverse the same trade twice

		$new_trade = new cTrade;
		$new_trade->status = "V";
		$new_trade->member_from = $this->member_from;
		$new_trade->member_to = $this->member_to;
		$new_trade->amount = -$this->amount;
		$new_trade->category = $this->category;
		$new_trade->description = "[Anulación de intercambio #". $this->trade_id." con fecha ". $this->trade_date." por administrador '". $user->member_id ."'] ". $description;
		$new_trade->type = "R";
		return $new_trade->MakeTrade($this->trade_id);
	}

}