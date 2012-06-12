<?php

/*
 An explanation of different member_decisions statuses in the trades_pending database...

 1 = Member hasn't made a decision regarding this trade - either it is Open or it has been Fulfilled (see 'status' column)
 2 = Member has removed this trade from his own records
 3 = Member has rejected this trade
 4 = Member has accepted that this trade has been rejected

*/

$config = Config::getInstance();

$tid = HTTPHelper::get("tid");
$action = HTTPHelper::rq("action");

$pending = new cTradesPending($_SESSION["user_login"]);

$table = new View("tables/trades-pending.phtml");

$page->membersCanInvoice = $config->legacy->MEMBERS_CAN_INVOICE;
$page->numToConfirm = $pending->numToConfirm;
$page->numToPay = $pending->numToPay;
$page->numToHaveConfirmed = $pending->numToHaveConfirmed;
$page->numToBePayed = $pending->numToBePayed;
$page->table = $table;

$master = PageView::getInstance();

function renderRow($t, $typ) {
	$id = urlencode($t["id"]);
	$row = array(
		"tradeDate" => $t["trade_date"],
		"from" => $t["member_id_from"],
		"to" => $t["member_id_to"],
		"amount" => $t["amount"],
		"description" => $t["description"],
		"actions" => array(),
	);

	if ($t["status"]=='O') {

		if ($typ=='P') {
			$row["actions"][] = array(
				"link" => "trades_pending.php?action=confirm&tid=$id",
				"text" => "Aceptar Pago",
			);
			$row["actions"][] = array(
				"link" => "trades_pending.php?action=reject&tid=$id",
				"text" => "Rechazar",
			);
		} elseif ($typ=='I') {
			$row["actions"][] = array(
				"link" => "trades_pending.php?action=confirm&tid=$id",
				"text" => "Enviar Pago",
			);
			$row["actions"][] = array(
				"link" => "trades_pending.php?action=reject&tid=$id",
				"text" => "Rechazar",
			);
		} elseif ($typ=='TBC') {
			if ($t["member_to_decision"]==3) {
				$row["actions"][] = array(
					"text" => $t["member_id_to"]." ha rechazada esta transacción.",
				);
				$row["actions"][] = array(
					"link" => "trades_pending.php?action=resend&tid=$id",
					"text" => "Resend Payment",
				);
				$row["actions"][] = array(
					"link" => "trades_pending.php?action=accept_rejection&tid=$id",
					"text" => "Quitar del listado",
				);
			} else {
				$row["actions"][] = array(
					"text" => "Pendiente de confirmación por " . $t["member_id_to"],
				);
			}
		} elseif ($typ=='TBP') {
			if ($t["member_to_decision"]==3) {
				$row["actions"][] = array(
					"text" => $t["member_id_to"]." ha rechazada esta transacción.",
				);
				$row["actions"][] = array(
					"link" => "trades_pending.php?action=resend&tid=$id",
					"text" => "Resend Invoice",
				);
				$row["actions"][] = array(
					"link" => "trades_pending.php?action=accept_rejection&tid=$id",
					"text" => "Quitar del listado",
				);
			} else {
				$row["actions"][] = array(
					"text" => "Awaiting Payment from " . $t["member_id_to"],
				);
			}
		}

	}else {

		if ($typ=='P')
			$row["actions"][] = array("text" => "¡Pago aceptado!");
		elseif ($typ=='I')
			$row["actions"][] = array("text" => "¡Pago enviado!");
		elseif ($typ=='TBC')
			$row["actions"][] = array($t["member_id_to"] . " ha confirmado");
		elseif ($typ=='TBP')
			$row["actions"][] = array($t["member_id_to"] . " has hecho la transferencia!");

		$row["actions"][] = array(
			"link" => "trades_pending.php?action=remove&tid=$id",
			"text" => "Quitar del listado ]",
		);
	}

	return $row;
}

function doTrade($t) {
	$member_to = new cMember;
	($t["typ"]=='T')
		? $member_to->LoadMember($_SESSION["user_login"])
		: $member_to->LoadMember($t["member_id_from"]);
	$member = new cMember;
	($t["typ"]=='T')
		? $member->LoadMember($t["member_id_from"])
		: $member->LoadMember($_SESSION["user_login"]);
	$trade = new cTrade($member, $member_to, $t['amount'], $t['category'], $t['description'], "T");
	$status = $trade->MakeTrade();
	if (!$status) {
		return false;
	}
	// Has the recipient got an income tie set-up? If so, we need to transfer a percentage of this elsewhere...
	$recipTie = cIncomeTies::getTie($member_to->member_id);
	if ($recipTie) {
		$theAmount = round(($t['amount']*$recipTie->percent)/100);
		$charity_to = new cMember;
		$charity_to->LoadMember($recipTie->tie_id);
		$trade = new cTrade($member_to, $charity_to, htmlspecialchars($theAmount), htmlspecialchars(12), htmlspecialchars("Donation from ".$member_to->member_id.""), 'T');
		$status = $trade->MakeTrade();
	}
	return true;
}

switch ($action) {

	case "resend":
		$sql = "SELECT * FROM trades_pending WHERE id = :tid LIMIT 0, 1";
		$row = PDOHelper::fetchRow($sql, array("tid" => $tid));
		// check if trade exists
		if (empty($row)) {
			$master->displayError("Este intercambio no existe.");
			break;
		}
		// check permission to act on this trade
		if ($row["member_id_from"] != $_SESSION["user_login"]) {
			$master->displayError("No tienes permisos para editar este intercambio.");
			break;
		}
		// check if not a 'still Open' trade
		if ($row["status"] != 'O') {
			$master->displayError("Lo siento, solo los intercambios abiertos pueden ser rechazados o reenviados.");
			break;
		}
		// check if really rejected
		if ($row["member_to_decision"] != 3) {
			$master->displayError("Este socio no ha rechazado la transacción.");
			break;
		}
		// update
		$out = PDOHelper::update("trades_pending", array("member_to_decision" => 1), "id = :id", array("id" => $row["id"]));
		$out
			? $master->setMessage("Transacción reenviado con exito.")
			: $master->displayError("Error actualizando la base de datos.");
		break;

	case "accept_rejection":
		$sql = "SELECT * FROM trades_pending WHERE id = :tid LIMIT 0, 1";
		$row = PDOHelper::fetchRow($sql, array("tid" => $tid));
		// check if exists
		if (empty($row)) {
			$master->displayError("Este intercambio no existe.");
			break;
		}
		// check permission to act on this trade
		if ($row["member_id_from"] != $_SESSION["user_login"]) {
			$master->displayError("No tienes permisos para editar este intercambio.");
			break;
		}
		// check if not a 'still Open' trade
		if ($row["status"] != 'O') {
			$master->displayError("Lo siento, solo los intercambios abiertos pueden ser rechazados o reenviados.");
			break;
		}
		// check if really rejected
		if ($row["member_to_decision"] != 3) {
			$master->displayError("Este socio no ha rechazado la transacción.");
			break;
		}
		// update
		$out = PDOHelper::update(DB::TRADES_PENDING, array("member_from_decision" => 4), "id = :id", array("id" => $row["id"]));
		$out
			? $master->setMessage("Transacción borrada con exito.")
			: $master->displayError("Error actualizando la base de datos.");
		break;

	case "reject":
		$sql = "SELECT * FROM trades_pending where id= :tid";
		$row = PDOHelper::fetchRow($sql, array("tid" => $tid));
		if (empty($row)) {
			$master->displayError("Este intercambio no existe o no tienes permisos para editarlo.");
			break;
		}
		// check permission to act on this trade
		if ($row["member_id_from"] != $_SESSION["user_login"]) {
			$master->displayError("No tienes permisos para editar este intercambio.");
			break;
		}
		// check if not a 'still Open' trade
		if ($row["status"] != 'O') {
			$master->displayError("Lo siento, solo los intercambios abiertos pueden ser rechazados o reenviados.");
			break;
		}
		if ($row["typ"] == 'T' and $row["member_id_to"] == $_SESSION["user_login"])  {
			// reject
			$out = PDOHelper::update(DB::TRADES_PENDING, array("member_to_decision" => 3), "id = :id", array("id" => $row["id"]));
		} elseif ($row["typ"] == 'I' and $row["member_id_to"] == $_SESSION["user_login"]) {
			// don't pay this invoice
			$out = PDOHelper::update(DB::TRADES_PENDING, array("member_to_decision" => 3), "id = :id", array("id" => $row["id"]));
		}
		// render
		$out
			? $master->setMessage("Member " . $row["member_id_from"] . " has been informed that you have rejected this transaction.")
			: $master->displayError("Error actualizando la base de datos.");
		break;

	case "remove":
		$sql = "SELECT * FROM trades_pending WHERE id = :id";
		$row = PDOHelper::fetchRow($sql, array("id" => $tid));
		if (empty($row)) {
			$master->displayError("Este intercambio no existe.");
			break;
		}
		// Do we have permission to act on this trade?
		if ($row["member_id_from"] != $_SESSION["user_login"] && $row["member_id_to"] != $_SESSION["user_login"]) {
			$master->displayError("No tienes permisos para editar este intercambio.");
			break;
		}
		// Check this is not a 'still Open' trade
		if ($row["status"] == 'O') {
			$master->displayError("Esta transacción esta todavia abierta y no puede ser borrada.");
			break;
		}
		// update
		if ($row["typ"] == 'T' and $row["member_id_from"] == $_SESSION["user_login"]) {
			// our sent payment has been confirmed
			$out = PDOHelper::update(DB::TRADES_PENDING, array("trades_pending set member_from_decision" => 2), "id = :id", array("id" => $tid));
		} elseif ($row["typ"] == 'T' and $row["member_id_to"] == $_SESSION["user_login"]) {
			// we have confirmed receipt of a payment
			$out = PDOHelper::update(DB::TRADES_PENDING, array("member_to_decision" => 2), "id = :id", array("id" => $tid));
		} elseif ($row["typ"] == 'I' and $row["member_id_from"] == $_SESSION["user_login"]) {
			// our invoice has been paid
			$out = PDOHelper::update(DB::TRADES_PENDING, array("member_from_decision" => 2), "id = :id", array("id" => $tid));
		} elseif ($row["typ"] == 'I' and $row["member_id_to"] == $_SESSION["user_login"]) {
			// we have now paid this invoice
			$out = PDOHelper::update(DB::TRADES_PENDING, array("member_to_decision" => 2), "id = :id", array("id" => $tid));
		}
		$out
			? $master->setMessage("Transacción borrada con exito.")
			: $master->displayError("Error actualizando la base de datos.");
		break;

	case "confirm":

		$q = "SELECT * FROM trades_pending where id=".$_GET["tid"]." limit 0,1";

		$result = $cDB->Query($q);

		if ($result && mysql_num_rows($result)>0) { // Trade Exists

			$row = mysql_fetch_array($result);

			if ($row["status"]!='O') {

				$list .= "<em>Este intercambio ya ha sido confirmado y esta cerrado.</em>";
				break;
			}

			/* What is the nature of the trade - Payment or Invoice? */

				if ($row["typ"]=='T') { // Payment - we are confirming receipt of incoming

					// Check we are the intended recipient
					if ($row["member_id_to"]!=$_SESSION["user_login"])

						$list .= "<em>No tienes permisos para confirmar este intercambio.</em>";
					else { // Action the trade

							if (!doTrade($row))
								$list .= "<font color=red>Error confirmando pago.</font>";
							else {

								$cDB->Query("UPDATE trades_pending set status=".$cDB->EscTxt('F')." where id=".$cDB->EscTxt($_GET["tid"])."");
								$list .= "<em> Has aceptado un pago de ".$row["amount"]." ".UNITS." de ".$row["member_id_from"]."</em>";
						}
					}
				}

				else if ($row["typ"]=='I') { // Invoice - we are sending a payment

						// Check we are the intended recipient of the invoice
					if ($row["member_id_to"]!=$_SESSION["user_login"])

						$list .= "<em>No tienes permisos para confirmar este intercambio.</em>";
					else { // Action the trade
							/*
							$goingFrom = $_SESSION["user_login"];
							$goingTo = $row["member_id_from"];

							$row["member_id_to"] = $goingTo;
							$row["member_id_from"] = $goingFrom;
							*/
							if (!doTrade($row)) {

								$member = new cMember;
								$member->LoadMember($_SESSION["user_login"]);
								if ($member->restriction==1) {
									$list .= LEECH_NOTICE;
								}
								else
									$list .= "<font color=red>Error enviando pago.</font>";
							}
							else {

								$cDB->Query("UPDATE trades_pending set status=".$cDB->EscTxt('F')." where id=".$cDB->EscTxt($_GET["tid"])."");
								$list .= "<em>Has enviado un pago de ".$row["amount"]." ".UNITS." to ".$row["member_id_from"]."</em>";
						}
					}
				}
			}


			else // This trade doesn't exist in the database!
				$list .= "<em>Este intercambio no existe en la base de datos!</em>";


	break;

	case "incoming":
		$sql = "SELECT * FROM trades_pending WHERE member_id_to = :to AND typ='T' AND member_to_decision = 1";
		$out = PDOHelper::fetchAll($sql, array("to" => $_SESSION["user_login"]));
		foreach ($out as $i => $row) {
			$out[$i] = renderRow($row, 'P');
		}
		$table->rows = $out;
		$table->title = "Ingresos para confirmar";
		break;

	case "outgoing":
		$sql = "SELECT * FROM trades_pending WHERE member_id_to = :to AND typ='I' AND member_to_decision = 1";
		$out = PDOHelper::fetchAll($sql, array("to" => $_SESSION["user_login"]));
		foreach ($out as $i => $row) {
			$out[$i] = renderRow($row, 'I');
		}
		$table->rows = $out;
		$table->title = "Pagos para hacer";
		break;

	case "payments_sent":
		$sql = "SELECT * FROM trades_pending where member_id_from = :from AND typ = 'T' AND member_from_decision = 1";
		$out = PDOHelper::fetchAll($sql, array("from" => $_SESSION["user_login"]));
		foreach ($out as $i => $row) {
			$out[$i] = renderRow($row, 'TBC');
		}
		$table->rows = $out;
		$table->title = "Pagos confirmados";
		break;

	case "invoices_sent":
		$sql = "SELECT * FROM trades_pending WHERE member_id_from = :from AND type = 'I' AND member_from_decision = 1";
		$out = PDOHelper::fetchAll($sql, array("from" => $_SESSION["user_login"]));
		foreach ($out as $i => $row) {
			$out[$i] = renderRow($row, 'TBP');
		}
		$table->rows = $out;
		$table->title = "Pagos hechos";
		break;

	default:
		$page->table = NULL;
}
