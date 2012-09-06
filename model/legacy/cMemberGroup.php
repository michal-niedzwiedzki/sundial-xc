<?php

class cMemberGroup {

	public $members = array();

	function LoadMemberGroup ($active_only=TRUE, $non_members=FALSE) {
		$exclusions = $active_only ? "status in ('A','L')" : "TRUE";
		if (!$non_members) {
			$exclusions .= " AND member_role != '9'";
		}
		$sql = "
			SELECT m.member_id FROM member AS m, person AS p
			WHERE m.member_id = p.member_id AND $exclusions
			ORDER BY p.first_name, p.last_name
		";
		$out = PDOHelper::fetchAll($sql, array());
		foreach ($out as $i => $row) {
			$this->members[$i] = new cMember();
			$this->members[$i]->LoadMember($row["member_id"]);
		}
		return !empty($out);
	}

	function MakeIDArray() {
		$ids = array();
		if ($this->members) {
			foreach($this->members as $member) {
				$ids[$member->member_id] = $member->PrimaryName() ." (". $member->member_id .")";
			}
		}
		return $ids;
	}

	public function getMembers() {
		return $this->members;
	}

	function MakeNameArray() {
		$names["0"] = "";
		if($this->members) {
			foreach($this->members as $member) {
				foreach ($member->person as $person) {
					$names[$member->member_id ."?". $person->person_id] = $person->first_name ." ". $person->mid_name ." (". $member->member_id .")";
				}
			}
			array_multisort($names);// sort purely by person name (instead of member, person)
		}
		return $names;
	}

	function DoNamePicker() {

		$tmp = '<script src=includes/autocomplete.js></script>';

		$mems = $this->MakeNameArray();

		$tmp .= "<select name=member_to>
			<option id=0 value=0>".count($mems)." soci@s...</option>";

		foreach($mems as $key=>$value) {

			$tmp .= "<option id='".$key."' value='".$key."'>".$value."</option>";
		}

		$tmp .= "</select>";
//		$form->addElement("select", "member_to", "...", $name_list->MakeNameArray());
		$tmp .= '<input type=text size=20 name=picker value="Buscar por nombre" onKeyUp="autoComplete(this,document.all.member_to,\'text\')"
			onFocus="this.value=\'\'">
			<!--<input type=button value="Update Dropdown List">-->';
		return $tmp;
	}

	// Use of this function requires the inclusion of class.listing.php
	function EmailListingUpdates($interval) {
		if(!isset($this->members)) {
			if(!$this->LoadMemberGroup())
				return false;
		}

		$listings = new cListingGroup(OFFER_LISTING);
		$since = new cDateTime("-". $interval ." days");
		$listings->LoadListingGroup(null,null,null,$since->MySQLTime());
		$offered_text = $listings->DisplayListingGroup(true);
		$listings = new cListingGroup(WANT_LISTING);
		$listings->LoadListingGroup(null,null,null,$since->MySQLTime());
		$wanted_text = $listings->DisplayListingGroup(true);

		$email_text = "";
		if($offered_text != "Ninguno encontrado")
			$email_text .= "<h2>Servicios Ofrecidos</h2><br>". $offered_text ."<p><br>";
		if($wanted_text != "Ninguno encontrado")
			$email_text .= "<h2>Servicios Solicitados</h2><br>". $wanted_text;
		if(!$email_text)
			return; // If no new listings, don't email

		$email_text = "<html><body>". LISTING_UPDATES_MESSAGE ."<p><br>".$email_text. "</body></html>";

		if ($interval == '1')
			$period = "de las últimas 24 horas";
		elseif ($interval == '7')
			$period = "de la última semana";
		else
			$period = "del último mes";

		foreach($this->members as $member) {
			if($member->email_updates == $interval and $member->person[0]->email) {
			  	mail($member->person[0]->email, SITE_SHORT_TITLE .": Listados nuevos o actualizados ". $period, wordwrap($email_text, 64), "From:". EMAIL_ADMIN ."\nMIME-Version: 1.0\n" . "Content-type: text/html; charset=utf-8");
			  //$member->esmail($member->person[0]->email, SITE_SHORT_TITLE .": Listados nuevos o actualizados del ultim@ ". $period, wordwrap($email_text, 64));
			}

		}

	}

	// Use of this function requires the inclusion of class.listing.php
	function ExpireListings4InactiveMembers() {
		if(!isset($this->members)) {
			if(!$this->LoadMemberGroup())
				return false;
		}

		foreach($this->members as $member) {
			if($member->DaysSinceLastTrade() >= MAX_DAYS_INACTIVE
			and $member->DaysSinceUpdatedListing() >= MAX_DAYS_INACTIVE) {
				$offer_listings = new cListingGroup(OFFER_LISTING);
				$want_listings = new cListingGroup(WANT_LISTING);

				$offered_exist = $offer_listings->LoadListingGroup(null, null, $member->member_id, null, false);
				$wanted_exist = $want_listings->LoadListingGroup(null, null, $member->member_id, null, false);

				if($offered_exist or $wanted_exist)	{
					$expire_date = new cDateTime("+". EXPIRATION_WINDOW ." days");
					if($offered_exist)
						$offer_listings->ExpireAll($expire_date);
					if($wanted_exist)
						$want_listings->ExpireAll($expire_date);

					if($member->person[0]->email != null) {
					  /*mail($member->person[0]->email,"Información importante sobre tu cuenta con ". SITE_SHORT_TITLE , wordwrap(EXPIRED_LISTINGS_MESSAGE, 64), "De:". EMAIL_ADMIN);*/
					  $member->esmail($member->person[0]->email,"Información importante sobre tu cuenta con ". SITE_SHORT_TITLE , wordwrap(EXPIRED_LISTINGS_MESSAGE, 64));
						$note = "";
						$subject_note = "";
					} else {
						$note = "\n\n***ATENCION: Este soci@ no tiene una cuenta de correo electrónico en el sistema. Hay que avisar por teléfono sobre la desactivación de sus listados. ";
						$subject_note = " (member has no email)";
					}

					mail(EMAIL_ADMIN, SITE_SHORT_TITLE ." listados caducados ". $member->member_id. $subject_note, wordwrap("Todos los servicios de este socio han sido desactivados debido a su inactividad. Se puede cambiar esta función en inc.config.php.". $note, 64) , "From:". EMAIL_ADMIN);
				}
			}
		}
	}

}