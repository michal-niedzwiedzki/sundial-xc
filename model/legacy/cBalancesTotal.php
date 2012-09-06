<?php

class cBalancesTotal {
	var $balance;

	function Balanced() {
		$sql = "SELECT sum(balance) AS balance FROM member";
		return PDOHelper::fetchCell("balance", $sql, array());
		//	cError::getInstance()->Error("No ha sido posible encontrar información de saldos ahora. Intentalo otra vez mas tarde.");
		//	return false;
	}

}