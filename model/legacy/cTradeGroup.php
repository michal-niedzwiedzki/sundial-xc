<?php

class cTradeGroup {

	var $trade;   	// an array of cTrade objects
	var $member_id;
	var $from_date;
	var $to_date;

	public function __construct($memberId = "%", $fromDate = LONG_LONG_AGO, $toDate = FAR_FAR_AWAY) {
		$this->member_id = $memberId;
		$this->from_date = $fromDate;
		$this->to_date = $toDate;
	}

	public function LoadTradeGroup($type = "all") {
		$tableName = DB::TRADES;
		$toDate = date('Y-m-d', strtotime("+1 days", strtotime($this->to_date)));
        if ("individual" == $type) {
			// select all trade_ids for this member
			$sql = "
				SELECT trade_id FROM $tableName
				WHERE (member_id_from LIKE :from OR member_id_to LIKE :to)
					AND trade_date > :start AND trade_date < :end
				ORDER BY trade_date DESC
			";
	        $out = PDOHelper::fetchAll($sql, array("from" => $this->member_id, "to" => $this->member_id, "start" => $this->from_date, "end" => $toDate));
		} else {
			// ignore monthly fees
			if (Config::getInstance()->legacy->SHOW_GLOBAL_FEES) {
				$sql = "
					SELECT trade_id FROM $tableName
					WHERE (member_id_from LIKE :from OR member_id_to LIKE :to)
						AND trade_date > :start AND trade_date < :end
					ORDER BY trade_date DESC
				";
		        $out = PDOHelper::fetchAll($sql, array("from" => $this->member_id, "to" => $this->member_id, "start" => $this->from_date, "end" => $toDate));
			} else {
				$sql = "
					SELECT trade_id FROM $tableName
					WHERE (member_id_from LIKE :from OR member_id_to LIKE :to)
						AND trade_date > :start AND trade_date < :end
						AND type <> 'S' AND type <> :type AND type <> :refund
					ORDER BY trade_date DESC
				";
		        $out = PDOHelper::fetchAll($sql, array("from" => $this->member_id, "to" => $this->member_id, "start" => $this->from_date, "end" => $toDate, "type" => cTrade::TRADE_MONTHLY_FEE, "refund" => cTrade::TRADE_MONTHLY_FEE_REVERSAL));
			}
        }

		// instantiate new cTrade objects and load them
		foreach ($out as $i => $row) {
			$this->trade[$i] = new cTrade();
			$this->trade[$i]->LoadTrade($row["trade_id"]);
		}
		return !empty($out);
	}

	public function DisplayTradeGroup() {
		$rows = array();
		$i = 0;
		foreach ((array)$this->trade as $trade) {
/*
            // Ignore monthly fees.
            if ($trade->type == TRADE_MONTHLY_FEE or $trade->type == TRADE_MONTHLY_FEE_REVERSAL) {
                continue;
            }
*/
			if ($trade->type == TRADE_REVERSAL or $trade->status == TRADE_REVERSAL)
				$fgcolor = "pink";
			elseif ($trade->member_to->member_id == $this->member_id)
				$fgcolor = "#4a5fa4";
			else
				$fgcolor = "#554f4f";

			$rows[] = array(
				"tradeDate" => new cDateTime($trade->trade_date),
				"fgColor" => $fgcolor,
				//$trade_date->ShortDate()
				"from" => $trade->member_from,
				"to" => $trade->member_to,
				"amount" => $trade->amount,
				"description" => $trade->description
			);
			++$i;
		}
		$page = new View("tables/trades.phtml");
		$page->rows = $rows;
		return $page;
	}

	function MakeTradeArray() {
		$trades = "";
		if($this->trade) {
			foreach($this->trade as $trade) {
				if($trade->type != "R" and $trade->status != "R") {
					$trades[$trade->trade_id] = "#". $trade->trade_id ." - ". $trade->amount ." ". UNITS . " FROM ". $trade->member_from->member_id ." TO ". $trade->member_to->member_id ." ON ". $trade->trade_date;
				}
			}
		}
		return $trades;
	}
}