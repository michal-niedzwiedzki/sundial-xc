<?php

final class TradeController extends Controller {

	/**
	 * @Title "Historial de intercambios"
	 */
	public function history_all() {
		$from = new cDateTime(HTTPHelper::rq("from"));
		$to = new cDateTime(HTTPHelper::rq("to"));

		$group = new cTradeGroup("%", $from->ShortDate(), $to->ShortDate());
		$group->LoadTradeGroup();

		$this->view->from = $from->ShortDate();
		$this->view->to = $to->ShortDate();
		$this->view->tradeGroup = $group->DisplayTradeGroup();
	}

	/**
	 * @Title "Historial de intercambios"
	 */
	public function history() {
		if (HTTPHelper::rq("mode") == "self") {
			$member = cMember::getCurrent();
		} else {
			$member = new cMember;
			$member->LoadMember(HTTPHelper::rq("member_id"));
			PageView::getInstance()->title = "Historial de intercambios para " . $member->PrimaryName();
		}

		$group = new cTradeGroup($member->member_id);
		$group->LoadTradeGroup("individual");

		$this->view->balance = $member->balance;
		$this->view->table = $group->DisplayTradeGroup();
	}

	/**
	 * @Title "Registrar un intercambio"
	 */
	public function index() {
		$config = Config::getInstance();
		$currentUser = User::getCurrent();
		$mode = HTTPHelper::rq("mode");
		$typ = HTTPHelper::rq("typ");

		$users = User::getAllActive();
		$usersList = array_map(function(User $user) { return $user->fullName; }, $users);

		$categories = Category::getAll();
		$categoriesList = array_map(function(Category $c) { return $c->description; }, $categories);

		$form = new TradeForm($user, $mode, $usersList, $categoiesList);
		$this->view->form = $form;

		if (!$form->validate()) {
			return;
		}
		$values = $form->process();

		if (isset($values['minutes']) and $values['minutes'] > 0) {
			$values['units'] = $values['units'] + ($values['minutes'] / 60);
		}
		if (!($values['units'] > 0)) {
			PageView::getInstance()->displayError("Número de horas no ha sido especificado");
			return;
		}

		$member_to_id = substr($values['member_to'], 0, strpos($values['member_to'], "?"));
		$member_to = new cMember;
		$member_to->LoadMember($member_to_id);

		if ($mode == "admin") {
			$user->MustBeLevel(1);
			$type = TRADE_BY_ADMIN; // record that trade was entered by an admin & log if logging enabled
			$member = new cMember();
			$member->LoadMember($values["member_id"]);
		} else {
			$type = TRADE_ENTRY;  // regular trade
			$member = $user;
		}

		if ($typ == 1 or $member_to->confirm_payments == 1) {

			// Payment
			if ($typ != 1) {
				if ($member->restriction == 1) {
					PageView::getInstance()->displayError(LEECH_NOTICE);
					return;
				}
				$insert = array(
					"trade_date" => date("Y-m-d H:i:s"),
					"member_id_from" => $member->member_id,
					"member_id_to" => $member_to_id,
					"amount" => $values["units"],
					"category" => $values["category"],
					"description" => $values["description"],
					"typ" => "T",
				);
				$out = PDOHelper::insert(DB::TRADES_PENDING, $insert);
				if (!$out) {
					PageView::getInstance()->displayError("El intercambio no ha funcionado! Intentalo otra vez mas tarde.");
					return;
				}

				LIVE and $member->esmail($member_to->person[0]->email, "Pago recibido en " . SITE_LONG_TITLE . "", "Hola ".$member_to_id.",\n\nHas recibido un pago nuevo de ".$member->member_id."\n\nComo has elegido confirmar todos tus pagos, tienes que entrar en tu cuenta y aceptar o rechazar este pago utlizando el siguiente enlace...\n\nhttp://".SERVER_DOMAIN.SERVER_PATH_URL."/trades_pending.php?action=incoming");
				PageView::getInstance()->setMessage($member_to_id." ha sido notificado que le quieres hacer una transferencia de ". $values['units'] ." ". strtolower(UNITS) .". Este soci@ tiene que confirmar la transacción.");
				return;
			}

			// Invoice
			if ($typ == 1) {
				if (!$config->legacy->MEMBERS_CAN_INVOICE) {
					PageView::getInstance()->displayError("Sorry, the Invoicing facility has been disabled by the site administrator.");
					return;
				}
				$insert = array(
					"trade_date" => date("Y-m-d H:i:s"),
					"member_id_from" => $member->member_id,
					"member_id_to" => $member_to_id,
					"amount" => $values["units"],
					"category" => $values["category"],
					"description" => $values["description"],
					"typ" => "I",
				);
				$out = PDOHelper::insert(DB::TRADES_PENDING, $insert);
				if (!$out) {
					PageView::getInstance()->displayError("El intercambio no ha funcionado! Intentalo otra vez mas tarde.");
					return;
				}

				LIVE and mail($member_to->person[0]->email, "Invoice Received on ".SITE_LONG_TITLE."", "Hi ".$member_to_id.",\n\nJust letting you know that you have received a new Invoice from ".$member->member_id."\n\nPlease log into your account now to pay or reject this invoice using the following URL...\n\nhttp://".SERVER_DOMAIN.SERVER_PATH_URL."/trades_pending.php?action=outgoing", EMAIL_FROM);
				PageView::getInstance()->setMessage($member_to_id." has been sent an invoice for ". $values['units'] ." ". strtolower(UNITS) .".<p> You will be informed when the member pays this invoice and will be invited to leave Feedback for this member.");
				return;
			}

		} else { // Make the trade

			if ($member->restriction == 1) {
				PageView::getInstance()->displayError(LEECH_NOTICE);
				return;
			}
			$trade = new cTrade($member, $member_to, $values['units'], $values['category'], $values['description'], $type);
			$out = $trade->MakeTrade();
			if (!$out) {
				PageView::getInstance()->displayError("El intercambio no ha funcionado! Intentalo otra vez mas tarde.");
				return;
			}
			PageView::getInstance()->setMessage("Ha hecho una transferencia de ". $values['units'] ." ". strtolower(UNITS) ." a ". $member_to_id .".");

			// Has the recipient got an income tie set-up? If so, we need to transfer a percentage of this elsewhere...
			$recipTie = cIncomeTies::getTie($member_to_id);
			if ($recipTie && $config->legacy->ALLOW_INCOME_SHARES) {
				$member_to = new cMember;
				$member_to->LoadMember($member_to_id);
				$theAmount = round(($values['units']*$recipTie->percent)/100);
				$charity_to = new cMember;
				$charity_to->LoadMember($recipTie->tie_id);
				$trade2 = new cTrade($member_to, $charity_to, $theAmount, 12, "Donation from ".$member_to_id, "T");
				$status = $trade2->MakeTrade();
			}

			return;
		}
	}

	/**
	 * @Title "Deshacer un intercambio"
	 * @Level 1
	 */
	public function reverse() {
		$trades = new cTradeGroup;
		$trades->LoadTradeGroup();

		$form = new TradeReverseForm($trades->MakeTradeArray());
		$this->view->form = $form;

		if (!$form->validate()) {
			return;
		}

		$form->freeze();
		$form->process();
		$values = $form->exportValues();
		$old_trade = new cTrade;
		$old_trade->LoadTrade($values["trade_id"]);
		$success = $old_trade->ReverseTrade($values["description"]);
		if ($success)
			PageView::getInstance()->setMessage("El intercambio ha sido cancelado.");
		else
			PageView::getInstance()->setMessage("Ha ocurrido un error, no ha sido posible deshacer el intercambio!");
	}

	/**
	 * @Title "Historial de intercambios"
	 */
	public function to_view() {
		if (HTTPHelper::rq("mode") == "self") {
			$member = cMember::getCurrent();
		} else {
			$member = new cMember();
			$member->LoadMember(HTTPHelper::rq("member_id"));
			PageView::getInstance()->title = "Historial de intercambios para " . $member->PrimaryName();
		}

		$color = ($member->balance > 0) ? "#4a5fa4" : "#554f4f";
		$group = new cTradeGroup($member->member_id);
		$group->LoadTradeGroup("individual");

		$this->view->tradeGroup = $group->DisplayTradeGroup();
		$this->view->color = $color;
		$this->view->balance = $member->balance;
	}

}