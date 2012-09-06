<?php

final class UsersMother {

	public static function create() {
		return self::createUserAccount(substr("u_" . (microtime(TRUE) - 1343345530), 0, 15), cMember::DEFAULT_PASSWORD);
	}

	public static function createRegularAccount($username, $password) {
		return self::createUserAccount($username, $password);
	}

	public static function createAdminAccount($username, $password) {
		return self::createUserAccount($username, $password, array("member_role" => 9));
	}

	public static function createSystemAccount($username, $password) {
		return self::createUserAccount($username, $password, array("account_type" => "S"));
	}

	public static function createUserAccount($username, $password, array $details = array()) {
		$row = array(
			"member_id" => $username,
			"password" => sha1($password),
			"member_role" => isset($details["member_role"]) ? $details["member_role"] : 0,
			"security_q" => NULL,
			"security_a" => NULL,
			"status" => isset($details["status"]) ? $details["status"] : "A",
			"member_note" => NULL,
			"admin_note" => "Account created by UsersMother",
			"join_date" => isset($details["join_date"]) ? $details["join_date"] : date("Y-m-d h:i:s"),
			"expire_date" => NULL,
			"away_date" => NULL,
			"account_type" => isset($details["account_type"]) ? $details["account_type"] : "O",
			"email_updates" => 7,
			"balance" => 0.00
		);
		PDOHelper::insert("member", $row);
		$row = array(
			"member_id" => $username,
			"primary_member" => "Y",
			"directory_list" => "Y",
			"first_name" => $username,
			"last_name" => "Mother",
			"mid_name" => NULL,
			"dob" => NULL,
			"mother_mn" => NULL,
			"email" => NULL,
			"address_city" => "Gotham",
			"address_state_code" => "",
			"address_post_code" => "00000",
			"address_country" => ""
		);
		PDOHelper::insert(DB::PERSONS, $row);
		$user = new cMember();
		$user->LoadMember($username);
		return $user;
	}

}