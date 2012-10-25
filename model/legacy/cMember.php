<?php

class cMember {

	const DEFAULT_PASSWORD = "password";

	var $person;  // this will be an array of cPerson class objects
	var $member_id;
	var $password;
	var $member_role;
	var $security_q;
	var $security_a;
	var $status;
	var $member_note;
	var $admin_note;
	var $join_date;
	var $expire_date;
	var $away_date;
	var $account_type;
	var $email_updates;
	var $balance;
	var $restriction;

	protected static $current;

	public function cMember($values = null) {
		if ($values) {
			$this->member_id = $values['member_id'];
			$this->password = $values['password'];
			$this->forgot_token = $values['forgot_token'];
			$this->forgot_expiry = $values['forgot_expiry'];
			$this->member_role = $values['member_role'];
			$this->security_q = $values['security_q'];
			$this->security_a = $values['security_a'];
			$this->status = $values['status'];
			$this->member_note = $values['member_note'];
			$this->admin_note = $values['admin_note'];
			$this->join_date = $values['join_date'];
			$this->expire_date = $values['expire_date'];
			$this->away_date = $values['away_date'];
			$this->account_type = isset($values['account_type']) ? $values['account_type'] : NULL;
			$this->email_updates = $values['email_updates'];
			$this->balance = $values['balance'];
		}
	}

	public static function getByEmail($email) {
		$sql = "
			SELECT m.member_id AS member_id FROM member AS m
			INNER JOIN person AS p USING (member_id)
			WHERE p.email = :email
		";
		try {
			$memberId = PDOHelper::fetchCell("member_id", $sql, array("email" => $email));
		} catch (Exception $e) {
			cError::getInstance()->Error("Error cargando datos de soci@.");
			return FALSE;
		}
		$member = new cMember();
		$member->LoadMember($memberId);
		return $member;
	}

	public function getId() {
		return $this->member_id;
	}

	public static function getCurrent() {
		if (!self::$current) {
			self::$current = new cMember();
			self::$current->RegisterWebUser();
		}
		return self::$current;
	}

	public function getEmail() {
		return isset($this->person[0]) ? $this->person[0]->email : NULL;
	}

	public function SaveNewMember() {
		return PDOHelper::insert("member", array(
			"member_id" => $this->member_id,
			"password" => sha1($this->password),
			"forgot_token" => $this->forgot_token,
			"forgot_expiry" => $this->forgot_expiry,
			"member_role" => $this->member_role,
			"security_q" => $this->security_q,
			"security_a" => $this->security_a,
			"status" => $this->status,
			"member_note" => $this->member_note,
			"admin_note" => $this->admin_note,
			"join_date" => $this->join_date,
			"expire_date" => $this->expire_date,
			"away_date" => $this->away_date,
			"account_type" => (string)$this->account_type,
			"email_updates" => $this->email_updates,
			"balance" => $this->balance
		));
	}

	public function RegisterWebUser() {
		if (isset($_SESSION["user_login"])) {
			$this->member_id = $_SESSION["user_login"];
			$this->LoadMember($_SESSION["user_login"]);
			// Session regeneration added to boost server-side security.
			session_regenerate_id();
		}
		self::$current = $this;
	}

	public function LoginFromCookie() {
		return false;
	}

	public static function IsLoggedOn() {
		return isset($_SESSION["user_login"]);
	}

	public function Login($user, $pass, $fromCookie = FALSE) {
		$loginHistory = new cLoginHistory();
		$tableName = DB::USERS;
		$sql = "
			SELECT member_id, password, member_role FROM $tableName
			WHERE member_id = :id AND password = sha(:password) AND status = 'A'
			LIMIT 1
		";
		$row = PDOHelper::fetchRow($sql, array("id" => $user, "password" => $pass));

		if ($row) {
			$loginHistory->RecordLoginSuccess($user);
			$this->DoLoginStuff($user, $row["password"]);
			return TRUE;
		} elseif ($fromCookie) {
			return FALSE;
		}
		$sql = "
			SELECT NULL FROM $tableName
			WHERE status = 'L' and member_id = :id
		";
		$row = PDOHelper::fetchRow($sql, array("id" => $user));
		empty($row)
			? cError::getInstance()->Error("Tu id de soci@ or contraseña is incorrecto.  Intentalo otra vez o visita esta pagina  <A HREF=password_reset.php>aqui</A> para obtener una nueva contraseña.")
			: cError::getInstance()->Error("Tu cuenta de usuario ha sido bloqueada, has intentado entrar demasiadas veces con una contraseña incorrecta. Pónte en contacto con nosotros para solucionar el problema");
		$loginHistory->RecordLoginFailure($user);
		return FALSE;
	}

	public function ValidatePassword($pass) {
		$tableName = DB::USERS;
		$sql = "
			SELECT count(*) AS c FROM $tableName
			WHERE member_id = :id AND password = sha(:password)
		";
		return (boolean)PDOHelper::fetchCell('c', $sql, array("id" => $this->member_id, "password" => $pass));
	}

	function UnlockAccount() {
		$history = new cLoginHistory;
		$has_logged_on = $history->LoadLoginHistory($this->member_id);
		if($has_logged_on) {
			$consecutive_failures = $history->consecutive_failures;
			$history->consecutive_failures = 0;  // Set count back to zero whether locked or not
			$history->SaveLoginHistory();
		}

		if($this->status == LOCKED) {
			$this->status = ACTIVE;
			if($this->SaveMember()) {
				return $consecutive_failures;
			}
		}
		return false;
	}

	function DeactivateMember() {
		if($this->status == ACTIVE) {
			$this->status = INACTIVE;
			return $this->SaveMember();
		} else {
			return false;
		}
	}

	function ReactivateMember() {
		if($this->status != ACTIVE) {
			$this->status = ACTIVE;
			return $this->SaveMember();
		} else {
			return false;
		}
	}

	public function ChangePassword($pass) { // TODO: Should use SaveMember and should reset $this->password
		$updates = array(
			"password" => sha1($pass),
			"forgot_token" => NULL,
			"forgot_expiry" => NULL,
		);
		$out = PDOHelper::update("member", $updates, "member_id = :id", array("id" => $this->member_id));
		if ($out) {
			return TRUE;
		}
		cError::getInstance()->Error("No se puede actualizar la contraseña ahora. Intentalo otra vez mas tarde");
		return FALSE;
	}

	public static function GeneratePassword() {
		return substr(md5(time()), 0, 7);
	}

	public function DoLoginStuff($user, $pass) {
		$this->LoadMember($user);
		$_SESSION["user_login"] = $user;
	}

	function UserLoginLogout() {
		if ($this->IsLoggedOn())
		{
			//$output = "<FONT SIZE=1><A HREF='".SERVER_PATH_URL."/member_logout.php'>Logout</A>&nbsp;&nbsp;&nbsp;";
			$output = "<A HREF='".SERVER_PATH_URL."/member_logout.php'>Salir</A>&nbsp;&nbsp;&nbsp;";
		} else {
			//$output = "<FONT SIZE=1><A HREF='".SERVER_PATH_URL."/member_login.php'>Login</A>&nbsp;&nbsp;&nbsp;";
			$output = "<A HREF='".SERVER_PATH_URL."/member_login.php'>Entrar</A>&nbsp;&nbsp;&nbsp;";
		}

		return $output;
	}

	public function MustBeLoggedOn() {
		if ($this->IsLoggedOn())
			return true;

		$_SESSION['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
		cError::getInstance()->SaveErrors();
		header("Location: " . HTTP_BASE . "/login_redirect.php");

		exit;
	}

	public function Logout() {
		self::$current = NULL;
		setcookie(session_name(), session_id(), time() - 42000, '/');
		$_SESSION = array();
		session_destroy();
	}

	public function MustBeLevel($level) {
		$this->MustBeLoggedOn(); // seems prudent to check first.
		if ($this->member_role < $level) {
			PageView::getInstance()->displayError("Lo siento, pero no dispones de permisos para hacer esto");
			Dispatcher::getInstance()->configure("Error", "forbidden");
			exit;
		}
	}

	public function AccountIsRestricted() {
		return $this->restriction == 1;
	}

	/**
	 * Loads user data from database
	 *
	 * @param int $id
	 * @param boolean $redirect on errors, default TRUE
	 * @return boolean
	 * @author unknown
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function LoadMember($id, $redirect = TRUE) {
		// fetch user data from database and populate object
		$sql = "
			SELECT * FROM member
			WHERE member_id = :id
			LIMIT 1
		";
		$row = PDOHelper::fetchRow($sql, array("id" => $id));
		if (empty($row)) {
			if ($redirect) {
				cError::getInstance()->Error("Erro cargando datos de soci@ (".$member.").  Intentalo otra vez mas tarde.");
				include("redirect.php");
			}
			return false;
		}
		foreach ($row as $column => $value) {
			$this->$column = $value;
		}

		// fetch associated records into person array
		$tableName = DB::PERSONS;
		$sql = "
			SELECT person_id FROM $tableName
			WHERE member_id = :id
			ORDER BY primary_member DESC, last_name, first_name
		";
		$out = PDOHelper::fetchAll($sql, array("id" => $id));
		if (empty($out)) {
			if ($redirect) {
				cError::getInstance()->Error("Hay un error accediendo a los datos de (".$member.").  Intentalo otra vez mas tarde.");
				include("redirect.php");
			}
			return false;
		}
		foreach ($out as $i => $row) {
			$this->person[$i] = new cPerson; // recursilvely instantiate person
			$this->person[$i]->LoadPerson($row["person_id"]);
		}

		return true;
	}

	function ShowMember()
	{
		$output = "Member Data:<BR>";
		$output .= $this->member_id . ", " . $this->password . ", " . $this->member_role . ", " . $this->security_q . ", " . $this->security_a . ", " . $this->status . ", " . $this->member_note . ", " . $this->admin_note . ", " . $this->join_date . ", " . $this->expire_date . ", " . $this->away_date . ", " . $this->account_type . ", " . $this->email_updates . ", " . $this->balance . "<BR><BR>";

		$output .= "Person Data:<BR>";

		foreach($this->person as $person)
		{
			$output .= $person->ShowPerson();
			$output .= "<BR><BR>";
		}

		return $output;
	}

	public function UpdateBalance($amount) {
		$this->balance += $amount;
		return $this->SaveMember();
	}

	/**
	 * Update member in database
	 *
	 * @author unknown
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 * @return boolean
	 */
	public function SaveMember() {
		$row = array(
			"password" => $this->password,
			"forgot_token" => $this->forgot_token,
			"forgot_expiry" => $this->forgot_expiry,
			"member_role" => $this->member_role,
			"security_q" => $this->security_q,
			"security_a" => $this->security_a,
			"status" => $this->status,
			"member_note" => $this->member_note,
			"admin_note" => $this->admin_note,
			"join_date" => $this->join_date,
			"expire_date" => $this->expire_date,
			"away_date" => $this->away_date,
			"account_type" => $this->account_type,
			"email_updates" => $this->email_updates,
			"confirm_payments" => $this->confirm_payments,
			"balance" => $this->balance
		);
		$out = PDOHelper::update("member", $row, "member_id = :id", array("id" => $this->member_id));
		if (!$out) {
			cError::getInstance()->Error("No ha sido posible guardar los cambios para el usuario '". $this->member_id ."'. Intentalo otra vez mas tarde.");
		}
		foreach($this->person as $person) {
			$person->SavePerson();
		}
		return (boolean)$out;
	}

	public function PrimaryName () {
		return $this->person[0]->first_name . " " . $this->person[0]->mid_name;
	}

	function VerifyPersonInAccount($person_id) { // Make sure hacker didn't manually change URL

		foreach($this->person as $person) {
			if($person->person_id == $person_id)
				return true;
		}
		cError::getInstance()->Error("Invalid person id in URL.  This break-in attempt has been reported.",ERROR_SEVERITY_HIGH);
		include("redirect.php");
	}

	function PrimaryAddress () {
		if($this->person[0]->address_street1 != "") {
			$address = $this->person[0]->address_street1 . ", ";
			if($this->person[0]->address_street2 != "")
				$address .= $this->person[0]->address_street2 . ", ";
		} else {
			$address = "";
		}

		return $address . $this->person[0]->address_city;
	}

	function AllNames () {
		foreach($this->person as $person) {
			if($person->primary_member == "Y") {
				$names = $person->first_name ." ". $person->mid_name ." ". $person->last_name;
			} else {
				$names .= ", ". $person->first_name ." ". $person->mid_name ." ". $person->last_name;
			}
		}
		return $names;
	}

	function AllPhones () {
		$phones = "";
		$reg_phones[]="";
		$fax_phones[]="";
		foreach($this->person as $person) {
			if($person->primary_member == "Y") {
				if($person->phone1_number != "") {
					$phones .= $person->DisplayPhone(1);
					$reg_phones[] = $person->DisplayPhone(1);
				}
				if($person->phone2_number != "") {
					$phones .= ", ". $person->DisplayPhone(2);
					$reg_phones[] = $person->DisplayPhone(2);
				}
				/*	if($person->fax_number != "") {
					$phones .= ", ". $person->DisplayPhone("fax"). " (Fax)";
					$fax_phones[] = $person->DisplayPhone("fax");
					}*/
			} else {
				if($person->phone1_number != "" and array_search($person->DisplayPhone(1), $reg_phones) === false){
					$phones .= ", ". $person->DisplayPhone(1). " (". $person->first_name .")";
					$reg_phones[] = $person->DisplayPhone(1);
				}
				if($person->phone2_number != "" and array_search($person->DisplayPhone(2), $reg_phones) === false) {
					$phones .= ", ". $person->DisplayPhone(2). " (". $person->first_name .")";
					$reg_phones[] = $person->DisplayPhone(2);
				}
				/*	if($person->fax_number != "" and array_search($person->DisplayPhone("fax"), $fax_phones) === false) {
					$phones .= ", ". $person->DisplayPhone("fax"). " (". $person->first_name ."'s Fax)";
					$fax_phones[] = $person->DisplayPhone("fax");
					}*/
			}
		}
		return $phones;
	}

	function AllEmails () {
		foreach($this->person as $person) {
			if($person->primary_member == "Y") {
				$emails = '<A HREF=email.php?email_to='. $person->email .'&member_to='. $this->member_id .'>'. $person->email .'</A>';
			} else {
				if($person->email != "" and strpos($emails, $person->email) === false)
					$emails .= ', <A HREF=email.php?email_to='. $person->email .'&member_to='. $this->member_id .'>'. $person->email .'</A> ('. $person->first_name .')';
			}
		}
		return $emails;
	}

	/**
	 * Return if user exists
	 *
	 * @param int $id
	 * @return boolean
	 * @author unknown
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function VerifyMemberExists($id) {
		$sql = "SELECT count(*) AS exists FROM member WHERE member_id = :id";
		return (boolean)PDOHelper::fetchCell("exists", $sql, array("id" => $id));
	}

	//function to send mails with special characters
	function esmail($mail, $sub, $mes){
	  $headers = "From: info@bancodetiempomalasana.com\r\n";
	  $headers .= "MIME-Version: 1.0\r\n";
	  $headers .= "Content-type: text/plain; charset=utf-8\r\n";
	  $headers .="Content-Transfer-Encoding: 8bit";

	  $mes=htmlspecialchars_decode($mes,ENT_QUOTES);//optional - I use encoding to POST data
	  return mail($mail, "=?utf-8?B?".base64_encode($sub)."?=", $mes, $headers);
	}

	public function MemberLink () {
		return "<a href=\"member_summary.php?member_id=". $this->member_id ."\">". $this->member_id ."</a>";
	}

	/**
	 * Return <img> tag with user's image
	 *
	 * @param int $id
	 * @return string
	 * @author unknown
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function DisplayMemberImg($id) {
		// check if images are turned off in config
		if (!defined("ALLOW_IMAGES") || !ALLOW_IMAGES) {
			return " ";
		}

		// fetch from database
		try {
			$sql = "SELECT filename, note FROM :tableName WHERE title = :title LIMIT 1";
			$row = PDOHelper::fetchRow($sql, array("tableName" => DB::UPLOADS, "title" => "mphoto_" . $id));
		} catch (Exception $e) {
			return " ";
		}

		// build file name
		if ($row["note"] == "no publicar") {
			return " ";
		}
		$filename = stripslashes($row["filename"]) ."_profile";
		$pathname = 'uploads/' . htmlentities($filename);
		return '<img src="' . $pathname . '" class="mugshot"/><br/>';
	}

	public function DisplayMember () {
		$sexArr = array("" => NULL, NULL => NULL, 1 => NULL, "M" => "Hombre", "F" => "Mujer");
		$view = new View("member");

		// personal details
		$view->memberId = $this->member_id;
		$view->name = $this->PrimaryName();
		$view->about = $this->person[0]->about_me;

		// statistics
		$stats = new cTradeStats($this->member_id);
		$view->mostRecentTrade = $stats->most_recent ? $stats->most_recent->ShortDate() : NULL;
		$view->totalTrades = $stats->total_trades;
		$view->totalUnits = $stats->total_units;

		// feedback
		$feedbackgrp = new cFeedbackGroup;
		$feedbackgrp->LoadFeedbackGroup($this->member_id);
		if(isset($feedbackgrp->feedback)) {
			$output .= "<b>Feedback:</b> <A HREF=feedback_all.php?mode=other&member_id=". $this->member_id . ">" . $feedbackgrp->PercentPositive() . "% positive</A> (" . $feedbackgrp->TotalFeedback() . " total, " . $feedbackgrp->num_negative ." negative & " . $feedbackgrp->num_neutral . " neutral)<BR>";
		}

		// contact details
		$view->joiningDate = new cDateTime($this->join_date);
		$view->sex = $sexArr[$this->person[0]->sex];
		$view->age = (cMember::age_from_dob($this->person[0]->dob) > 120) ? '--' : cMember::age_from_dob($this->person[0]->dob);
		$view->email = $this->person[0]->email;
		$view->phone1 = $this->person[0]->DisplayPhone(1);
		$view->phone2 = $this->person[0]->DisplayPhone(2);
		$view->address2 = NULL;
		$view->postCode = $this->person[0]->address_post_code;
		$view->city = $this->person[0]->address_city;

		return $view;
	}

	public function MakeJointMemberArray() {
		$names = array();
		foreach ($this->person as $person) {
			if ($person->primary_member != 'Y') {
				$names[$person->person_id] = $person->first_name . " " . $person->last_name;
			}
		}
		return $names;
	}

	public function DaysSinceLastTrade() {
		$tableName = DB::TRADES;
		$sql = "SELECT max(trade_date) AS trade_date FROM $tableName WHERE member_id_to = :to OR member_id_from = :from";
		$tradeDate = PDOHelper::fetchCell("trade_date", $sql, array("to" => $this->member_id, "from" => $this->member_id));
		$tradeDate
			? $lastTrade = new cDateTime($tradeDate)
			: $lastTrade = new cDateTime($this->join_date);
		return $lastTrade->DaysAgo();
	}

	public function DaysSinceUpdatedListing() {
		$tableName = DB::LISTINGS;
		$sql = "SELECT max(posting_date) AS posting_date FROM $tableName WHERE member_id = :id";
		$postingDate = PDOHelper::fetchCell("posting_date", $sql, array("id" => $this->member_id));
		if ($postingDate) {
			$last_update = new cDateTime($postingDate);
		} else {
			$last_update = new cDateTime($this->join_date);
		}
		return $last_update->DaysAgo();
	}

	public function age_from_dob($dob) {
		if (!$dob) {
			return 0;
		}
		list($y, $m, $d) = explode('-', $dob);
		if (($m = (date('m') - $m)) < 0) {
			$y++;
		} elseif ($m == 0 && date('d') - $d < 0) {
			$y++;
		}
		return date('Y') - $y;
	}

}