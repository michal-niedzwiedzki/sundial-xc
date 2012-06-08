<?php

final class MemberController extends Controller {

	/**
	 * @Level 1
	 * @Title "Seleccionar socio para cambio"
	 */
	public function choose() {
		$action = HTTPHelper::rq("action");
		$get1 = HTTPHelper::rq("get1");
		$get1val = HTTPHelper::rq("get1val");
		$inactive = HTTPHelper::rq("inactive");

		$ids = new cMemberGroup;
		$inactive ? $ids->LoadMemberGroup(false, true) : $ids->LoadMemberGroup();
		$idsArray = $ids->MakeIDArray();

		$form = new MemberChooseForm($action, $ids->MakeIDArray());
		if ($get1) {
			$form->addElement("hidden", "get1", $get1);
			$form->addElement("hidden", "get1val", $get1val);
		}

		$this->page->form = $form;

		if (!$form->validate()) {
			return;
		}
		
		$form->freeze();
		$form->process();
		$values = $form->exportValues();
		$user = cMember::getCurrent();
		if ($get1)
			$get_string = "&". $get1 ."=". $get1val;
		else
			$get_string = "";
		header("Location: ".HTTP_BASE."/". $action .".php?mode=admin&member_id=".$values["member_id"] . $get_string);
		exit;
	}

	public function contact_choose() {
		include ROOT_DIR . "/legacy/member_contact_choose.php";
	}

	public function contact_create() {
		include ROOT_DIR . "/legacy/member_contact_create.php";
	}

	public function contact_delete() {
		include ROOT_DIR . "/legacy/member_contact_delete.php";
	}

	public function contact_edit() {
		include ROOT_DIR . "/legacy/member_contact_edit.php";
	}

	public function contact_to_edit() {
		include ROOT_DIR . "/legacy/member_contact_to_edit.php";
	}

	/**
	 * @Title "Crear socio nuevo"
	 * @Level 1
	 */
	public function create() {
		$form = new MemberCreateForm();
		$this->page->form = $form;

		$config = Config::getInstance();
		if (!$form->validate()) {
			return;
		}

		$form->freeze();
		$form->process();

		// Following are default values for which this form doesn't allow input
		$values['security_q'] = "";
		$values['security_a'] = "";
		$values['status'] = "A";
		$values['member_note'] = "";
		$values['expire_date'] = "";
		$values['away_date'] = "";
		$values['balance'] = 5;
		$values['primary_member'] = "Y";
		$values['directory_list'] = "Y";

		$date = $values['join_date'];
		$values['join_date'] = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
		$date = $values['dob'];
		$values['dob'] = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
		if($values['dob'] == $today['year']."/".$today['mon']."/".$today['mday'])
			$values['dob'] = ""; // if birthdate was left as default, set to null
		$values['phone1_number'] = $values['phone1'];
		$values['phone2_number'] = $values['phone2'];

		$new_member = new cMember($values);
		$new_person = new cPerson($values);

		if($created = $new_person->SaveNewPerson()) 
			$created = $new_member->SaveNewMember();

		if($created) {
			$list .= "Nuevo soci@ creado. Pulse <A HREF=member_create.php>aqui</A> para crear otra cuenta.";
			if($values['email'] == "") {
				$list .= "Este soci@ no tiene correo electronico, habra que informarle de su ID ('". $values["member_id"]. "') y contraseña ('". $values["password"] ."').";	
			} else {
			  //$mailed = mail($values['email'], NEW_MEMBER_SUBJECT, NEW_MEMBER_MESSAGE . "\n\nID de soci@: ". $values['member_id'] ."\n". "Contraseña: ". $values['password'], EMAIL_FROM);
			  $mailed = $new_member->esmail($values['email'], NEW_MEMBER_SUBJECT, NEW_MEMBER_MESSAGE . "\n\nID de soci@: ". $values['member_id'] ."\n". "Contraseña: ". $values['password']);
				if($mailed)
					$list .= "Un correo ha sido enviado a '". $values["email"] ."' con el id de usuario y la contraseña.";
				else
					$list .= " No ha sido posible enviar un correo al soci@ nuevo, posiblemente debido a un problema tecnico. Sería necesario avisar al soci@ de su ID ('". $values["member_id"]. "') y contraseña ('". $values["password"] ."').</I>";	 
			}
		} else {
			cError::getInstance()->Error("Un error ha ocurrido en el momento de guardar los datos. Intentalo otra vez mas tarde");
		}
		PageView::getInstance()->displayPage($list);
	}

	/**
	 * @Title "Directorio de Socios"
	 */
	public function directory() {
		$user = cMember::getCurrent();

		$this->page->searchId = HTTPHelper::rq("uID");
		$this->page->searchName = HTTPHelper::rq("uName");
		$this->page->searchOrder = HTTPHelper::rq("orderBy");
		
		$member_list = new cMemberGroup();
		//$member_list->LoadMemberGroup();
		
		// How should results be ordered?
		switch(HTTPHelper::rq("orderBy")) {
			case "fm":
				$orderBy = 'ORDER BY p.first_name, p.mid_name ';
				break;
			case "idA":
				$orderBy = 'ORDER BY m.member_id';
				break;
			case "fml":
				$orderBy = 'ORDER BY p.mid_name, p.first_name DESC';
				break;
			case "lf":
				$orderBy = 'ORDER BY p.last_name, first_name DESC';
				break;
			default:
				$orderBy = 'ORDER BY m.member_id';
		}
		
		// SQL condition string
		$condition = "TRUE";
		$params = array();
		
		if (HTTPHelper::rq("uID")) { // We're searching for a specific member ID in the SQL
			$condition .= " AND m.member_id = :id";
			$params["id"] = trim(HTTPHelper::rq("uID"));
		}
		
		if (HTTPHelper::rq("uName")) { // We're searching for a specific username in the SQL
			$uName = trim(HTTPHelper::rq("uName"));
			// Does it look like we've been provided with a first AND last name?
			$uName = explode(" ", $uName);
			$nameSrch = "p.first_name LIKE :firstName";
			$params["firstName"] = "%" . trim($uName[0]) . "%";
			if (isset($uName[1])) { // surname provided
				$nameSrch .= " OR p.middle_name LIKE :middleName";
				$params["middleName"] = "%" . trim($uName[1]) . "%";
			} else { // No surname, but term entered may be surname so apply to that too
				$nameSrch .= " OR p.middle_name LIKE :middleName";
				$params["middleName"] = "%" . trim($uName[0]) . "%";
			}
			$condition .= " AND ($nameSrch)";
		}
		
		if (HTTPHelper::rq("uLoc")) { // We're searching for a specific Location in the SQL
			$condition .= " AND (p.address_post_code LIKE :postCode OR p.address_street2 LIKE :street2 OR p.address_city LIKE :city OR p.address_country LIKE :country";
			$params["postCode"] = "%" . trim(HTTPHelper::rq("uLoc")) . "%";
			$params["street2"] = "%" . trim(HTTPHelper::rq("uLoc")) . "%";
			$params["city"] = "%" . trim(HTTPHelper::rq("uLoc")) . "%";
			$params["country"] = "%" . trim(HTTPHelper::rq("uLoc")) . "%";
		}
		
		// Do search in SQL
		$membersTableName = DB::MEMBERS;
		$personsTableName = DB::PERSONS;
		$sql = "
			SELECT m.member_id FROM $membersTableName AS m, $personsTableName AS p
			WHERE m.member_id = p.member_id AND primary_member = 'Y' AND $condition $orderBy
		";
		$out = PDOHelper::fetchAll($sql, $params);
		foreach ($out as $i => $row) {
			$member_list->members[$i] = new cMember();
			$member_list->members[$i]->LoadMember($row["member_id"]);
		}
		
		$rows = array();
		$showInactive = Config::getInstance()->legacy->SHOW_INACTIVE_MEMBERS;
		if (!empty($member_list->members)) {
			foreach ($member_list->members as $i => $member) {
				if ($member->status != "I" or $showInactive) { // force display of inactive members off, unless specified otherwise in config file
					if ($member->account_type != "F") {  // Don't display fund accounts
						$rows[] = array(
							"id" => $member->member_id,
							"names" => $member->AllNames(),
							"phones" => $member->AllPhones(),
							"balance" => $member->balance,
						);
					}
				} // end loop to force display of inactive members off
			}
		}
		
		$table = new View("tables/members.phtml");
		$table->rows = $rows;
		$table->displayBalance = Config::getInstance()->legacy->MEM_LIST_DISPLAY_BALANCE or $user->member_role >= 1;
		
		$this->page->table = $table;
	}

	public function edit() {
		include ROOT_DIR . "/legacy/member_edit.php";
	}

	/**
	 * @Public
	 * @Title "Log in"
	 */
	public function login() {
		$config = Config::getInstance();
		$user = cMember::getCurrent();

		$this->page->title = $config->site->title;
		$this->page->isLoggedOn = $user->IsLoggedOn();
		$this->page->isRestricted = $user->AccountIsRestricted();
	}

	/**
	 * @Public
	 * @Title "Log out"
	 */
	public function logout() {
		cMember::getCurrent()->Logout();
	}

	public function photo_upload() {
		include ROOT_DIR . "/legacy/member_photo_upload.php";
	}

	public function profile_all_in_one() {
		include ROOT_DIR . "/legacy/member_profile_all_in_one.php";
	}

	/**
	 * @Title "Perfil de socio"
	 */
	public function profile() {
		$this->page->memberId = cMember::getCurrent()->member_id;
	}

	public function status_change() {
		include ROOT_DIR . "/legacy/member_status_change.php";
	}

	public function summary() {
		$member = new cMember();
		$member->LoadMember(HTTPHelper::rq("member_id"));

		$offeredListings = new cListingGroup(OFFER_LISTING);
		$offeredListings->LoadListingGroup(null, null, HTTPHelper::rq("member_id"));
		$wantedListings = new cListingGroup(WANT_LISTING);
		$wantedListings->LoadListingGroup(null, null, HTTPHelper::rq("member_id"));

		$this->page->contactDetails = $member->DisplayMember();
		$this->page->offered = $offeredListings->DisplayListingGroup();
		$this->page->wanted = $wantedListings->DisplayListingGroup();

		$master = PageView::getInstance()->title = "Perfil de " . $member->PrimaryName();
	}

	public function to_edit_photo() {
		include ROOT_DIR . "/legacy/member_to_edit_photo.php";
	}

	/**
	 * @Title "Elegir soci@ para editar"
	 * @Level 1
	 */
	public function to_edit() {
		$ids = new cMemberGroup;
		$ids->LoadMemberGroup(null,true);

		$form = new UsersListForm($ids->MakeIDArray());
		$this->page->form = $form;

		if (!$form->validate()) {
			return;
		}
		$form->freeze();
		$form->process();
		$values = $form->exportValues();
		header("Location: ".HTTP_BASE."/member_edit.php?mode=admin&member_id=".$values["member_id"]);
	}

	public function unlock() {
		include ROOT_DIR . "/legacy/member_unlock.php";
	}

}