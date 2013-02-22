<?php

class cLoginHistory {

	var $member_id;
	var $total_failed;
	var $consecutive_failures;
	var $last_failed_date;
	var $last_success_date;

	// NEED TO MODIFY MEMBER CLASS TO SET STATUS FIELD TO LOCKED AND CHECK DURING LOGIN

	public function LoadLoginHistory($memberId) {
		$tableName = DB::LOGINS;
		$sql = "
			SELECT total_failed, consecutive_failures, last_failed_date, last_success_date
			FROM $tableName WHERE member_id = :id
		";
		$row = PDOHelper::fetchRow($sql, array("id" => $memberId));
		if (empty($row)) {
			return FALSE;
		}
		$this->member_id = $memberId;
		$this->total_failed = $row["total_failed"];
		$this->consecutive_failures = $row["consecutive_failures"];
		$this->last_failed_date = $row["last_failed_date"];
		$this->last_success_date = $row["last_success_date"];
		return TRUE;
	}

	public function SaveLoginHistory () {
		$params = array(
			"total_failed" => $this->total_failed,
			"consecutive_failures" => $this->consecutive_failures,
			"last_failed_date" => $this->last_failed_date,
			"last_success_date" => $this->last_success_date,
		);
		$whereParams = array("member_id" => $this->member_id);
		if (PDOHelper::update(DB::LOGINS, $params, "member_id = :id", array("id" => $this->member_id))) {
			return TRUE;
		}
		PageView::getInstance()->displayError("Could not save changes to login history '". $this->member_id ."'. Please try again later.");	
		return FALSE;
	}

	public function SaveNewLoginHistory() {
		$params = array(
			"member_id" => $this->member_id,
			"total_failed" => $this->total_failed,
			"consecutive_failures" => $this->consecutive_failures,
			"last_failed_date" => $this->last_failed_date,
			"last_success_date" => $this->last_success_date,
		);
		return (boolean)PDOHelper::insert(DB::LOGINS, $params);
	}

	function RecordLoginSuccess ($member_id) {
		if($this->LoadLoginHistory($member_id)) {
			$this->last_success_date = $this->CurrentTimestamp();
			$this->consecutive_failures = 0;
			return $this->SaveLoginHistory();
		} else {
			$this->member_id = $member_id;
			$this->total_failed = 0;
			$this->consecutive_failures = 0;
			$this->last_success_date = $this->CurrentTimestamp();
			$this->last_failed_date = "00000000000000"; // MySQL won't allow a timestamp to be NULL
			return $this->SaveNewLoginHistory();
		}
	}

	public function RecordLoginFailure($member_id) {
		if ($this->LoadLoginHistory($member_id)) {
			$this->last_failed_date = $this->CurrentTimestamp();
			$this->consecutive_failures += 1;
			$this->total_failed += 1;
			if($this->consecutive_failures > FAILED_LOGIN_LIMIT) {
				$member = new cMember;
				$member->LoadMember($member_id);
				$member->status = LOCKED;
				$member->SaveMember();
			}
			return $this->SaveLoginHistory();
		} else {
			$sql = "SELECT count(*) AS c FROM member WHERE member_id = :id";
			$c = PDOHelper::fetchCell("c", $sql, array("id" => $member_id));
			if ($c != 1) {
				// Userid must have been misspelled or didn't exist.
				return FALSE;
			}
			$this->member_id = $member_id;
			$this->total_failed = 1;
			$this->consecutive_failures = 1;
			$this->last_failed_date = $this->CurrentTimestamp();
			$this->last_success_date = "00000000000000"; // MySQL won't allow a timestamp to be NULL
			return $this->SaveNewLoginHistory();
		}
	}

	public function CurrentTimestamp() {
		// TODO: Move to a new class (maybe "class.time_date.php")...
		// Also probably shouldn't depend on the default string format.
		$date = getdate();
		$now = $date["year"] . str_pad($date["mon"],2,"0", STR_PAD_LEFT) . str_pad($date["mday"],2,"0", STR_PAD_LEFT) . str_pad($date["hours"],2,"0", STR_PAD_LEFT) . str_pad($date["minutes"],2,"0", STR_PAD_LEFT) . str_pad($date["seconds"],2,"0", STR_PAD_LEFT);
		return $now;
	}

}