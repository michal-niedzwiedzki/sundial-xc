<?php

class cTradesPending {

	var $numPending = 0; // Total num trades pending

	var $numIn = 0; // Number of trades directed TO us that we must act on
	var $numOut = 0; // Number of trades sent FROM us that we are awaiting action on

	var $numToPay = 0; // Num Invoices we need to pay
	var $numToConfirm = 0; // Num payments we need to confirm
	var $numToBePayed = 0; // Num invoices awaiting payment on
	var $numToHaveConfirmed = 0; // Num payments awaiting confirmation on

	public function __construct($memberID) {
		// Get all trades involving this memberID that are currently marked as Open
		$sql = "SELECT * FROM trades_pending WHERE status = 'O' AND (member_id_to = :to OR member_id_from = :from)";
		$out = PDOHelper::fetchAll($sql, array("from" => $memberID, "to" => $memberID));
		if (empty($out)) { // None found = none pending!
			return;
		}
		foreach ($out as $row) {
			// Is this - An Invoice TO memberID that hasn't yet been acted on?
			if ($row["typ"]=="I" && $row["member_id_to"]==$memberID && $row["member_to_decision"]==1) {
				$this->numToPay += 1;
			}
			// Is this - A Payment TO memberID that hasn't yet been acted on?
			if ($row["typ"]=="T" && $row["member_id_to"]==$memberID && $row["member_to_decision"]==1) {
				$this->numToConfirm += 1;
			}
			// Is this - An Invoice FROM memberID that hasn't yet been acted on?
			if ($row["typ"]=="I" && $row["member_id_from"]==$memberID && $row["member_from_decision"]==1) {
				$this->numToBePayed += 1;
			}
			// Is this - An Payment FROM memberID that hasn't yet been acted on?
			if ($row["typ"]=="T" && $row["member_id_from"]==$memberID && $row["member_from_decision"]==1) {
				$this->numToHaveConfirmed += 1;
			}
		}
		$this->numIn = $this->numToPay + $this->numToConfirm;
		$this->numOut = $this->numToBePayed + $this->numToHaveConfirmed;
		$this->numPending = $this->numIn + $this->numOut;
	}

}