<?php

final class MemberController extends Controller {

	/**
	 * @Admin
	 * @Title "Seleccionar socio para cambio"
	 */
	public function choose() {
		$whereTo = HTTPHelper::rq("action");
		$get1 = HTTPHelper::rq("get1");
		$get1val = HTTPHelper::rq("get1val");
		$inactive = HTTPHelper::rq("inactive");

		$inactive
			? $users = User::getAll()
			: $users = User::getAllActive();
		$ids = array_map(function(User $user) { return $user->fullName; }, $users);

		$form = new MemberChooseForm($action, $ids);
		if ($get1) {
			$form->addElement("hidden", "get1", $get1);
			$form->addElement("hidden", "get1val", $get1val);
		}
		$this->view->form = $form;

		if (!$form->validate()) {
			return;
		}
		$values = $form->process();
		$get_string = $get1
			? "&". $get1 ."=". $get1val
			: $get_string = "";
		HTTPHelper::redirectSeeOther(HTTP_BASE . "/". $action .".php?mode=admin&member_id=".$values["member_id"] . $get_string);
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
	 * @Admin
	 */
	public function create() {
		$form = new MemberCreateForm();
		if (!$form->validate()) {
			$this->view->form = $form;
			return;
		}
		$values = $form->process();

		// save user
		$user = new User();
		$user->login = $values["login"];
		$user->password = $values["password"];
		$user->name = $values["name"];
		$user->fullName = $values["name"] . " " . $values["surname"];
		$user->email = $values["email"];
		$user->isAdmin = $values["admin"];
		$user->state = User::STATE_INACTIVE;
		$user->bornOn = $values["dob"]["unix"];
		if (!$user->save()) {
			return PageView::getInstance()->displayError("Un error ha ocurrido en el momento de guardar los datos. Intentalo otra vez mas tarde");
		}

		// send email to user
		$config = Config::getInstance();
		$message = new EmailMessage($config->admin->email, "Registration confirmation", "ID de soci@: ". $values['login'] ."\n". "Contraseña: ". $values['password']);
		$message->to($user);
		$message->save();

		// redirect to user's profile
		PageView::getInstance()->setMessage("Nuevo socio creado");
		HTTPHelper::redirectSeeOther(Link::to("member", "summary", array("id" => $user->id)));
	}

	/**
	 * @Title "Directorio de Socios"
	 */
	public function directory() {
		$config = Config::getInstance();
		$currentUser = cMember::getCurrent();

		$this->view->searchId = HTTPHelper::rq("uID");
		$this->view->searchName = HTTPHelper::rq("uName");
		$this->view->searchOrder = HTTPHelper::rq("orderBy");

		// set order
		$filter = new UserFilter();
		switch(HTTPHelper::rq("orderBy")) {
			case "fm":
				$filter->orderByName();
				break;
			default:
				$filter->orderById();
				break;
		}

		// SQL condition string
		$condition = "TRUE";
		$params = array();

		// search by user id
		($id = HTTPHelper::rq("uID")) and $filter->id($id);

		// search by login or full name
		($phrase = HTTPHelper::rq("uName")) and $filter->text($phrase);

		// disable inactive users if set in config
		$config->legacy->SHOW_INACTIVE_MEMBERS and $filter->active();

		// fetch users
		$users = User::filter($filter);

		$table = new View("tables/members");
		$table->users = $users;
		$table->displayBalance = $config->legacy->MEM_LIST_DISPLAY_BALANCE or $currentUser->isAdmin();
		$this->view->table = $table;
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

		$this->view->title = $config->site->title;
		$this->view->isLoggedOn = $user->IsLoggedOn();
		$this->view->isRestricted = $user->AccountIsRestricted();
		$this->view->csrf = CSRF;
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
		$currentUser = User::getCurrent();
		$id = HTTPHelper::rq("id");
		($id and $currentUser->isAdmin())
			? $this->view->user = User::getById($id)
			: $this->view->user = $currentUser;
	}

	public function status_change() {
		include ROOT_DIR . "/legacy/member_status_change.php";
	}

	public function summary() {
		$member = new cMember();
		$member->LoadMember(HTTPHelper::rq("member_id"));

		$offeredListings = new cListingGroup(OFFER_LISTING);
		$offeredListings->LoadListingGroup(NULL, NULL, HTTPHelper::rq("member_id"));
		$wantedListings = new cListingGroup(WANT_LISTING);
		$wantedListings->LoadListingGroup(NULL, NULL, HTTPHelper::rq("member_id"));

		$this->view->contactDetails = $member->DisplayMember();
		$this->view->offered = $offeredListings->DisplayListingGroup();
		$this->view->wanted = $wantedListings->DisplayListingGroup();

		$master = PageView::getInstance()->title = "Perfil de " . $member->PrimaryName();
	}

	/**
	 * @Title "Elegir soci@ para subir o cambiar foto"
	 * @Level 1
	 */
	public function to_edit_photo() {
		$ids = new cMemberGroup();
		$ids->LoadMemberGroup(NULL, TRUE);

		$form = new MemberChooseForm(NULL, $ids->MakeIDArray());
		$this->view->form = $form;

		if (!$form->validate()) {
			return;
		}
		$form->freeze();
		$form->process();
		$values = $form->exportValues();
		header("Location: " . HTTP_BASE . "/member_photo_upload.php?mode=admin&member_id=" . urlencode($values["member_id"]));
	}

	/**
	 * @Title "Elegir soci@ para editar"
	 * @Level 1
	 */
	public function to_edit() {
		$ids = new cMemberGroup;
		$ids->LoadMemberGroup(null,true);

		$form = new UsersListForm($ids->MakeIDArray());
		$this->view->form = $form;

		if (!$form->validate()) {
			return;
		}
		$form->freeze();
		$form->process();
		$values = $form->exportValues();
		header("Location: " . HTTP_BASE . "/member_edit.php?mode=admin&member_id=" . $values["member_id"]);
	}

	/**
	 * @Title "Desbloquear cuenta y crear contraseña nueva"
	 * @Level 1
	 */
	public function unlock() {

		$ids = new cMemberGroup;
		$ids->LoadMemberGroup();

		$form = FormHelper::standard();
		$form->addElement("select", "member_id", "Seleccionar cuenta", $ids->MakeIDArray());
		$form->addElement("submit", "btnSubmit", "Desbloquear");
		$form->addElement("radio", "emailTyp", "", "Enviar correo con nueva contraseña","pword");
		$form->addElement("radio", "emailTyp", "", "Mostrar la contraseña nueva en pantalla","show_pword");

		$this->view->form = $form;

		if (!$form->validate()) {
			return;
		}
		$values = $form->process();
		$member = new cMember();
		$member->LoadMember($values["member_id"]);

		$message = "";
		if ($consecutive_failures = $member->UnlockAccount()) {
			$message .= "Esta cuenta estaba bloqueada debido a ". $consecutive_failures ." intentos fallidos de entrar. Ahora esta activa otra vez. ";
		}
		$password = $member->GeneratePassword();
		$member->ChangePassword($password);
		$message .= "La nueva contraseña ha sido generada";

		$whEmail = "'Contraseña cambiada'";
		if (HTTPHelper::rq("emailTyp") == 'pword') {
			$mailed = LIVE
				? $member->esmail($member->person[0]->email, PASSWORD_RESET_SUBJECT, PASSWORD_RESET_MESSAGE . "\n\nID de soci@: ". $member->member_id ."\nNueva contraseña: ". $password)
				: TRUE;
			$message .= $mailed
				? " y un correo de $whEmail ha sido enviado a la dirección del soci@ (". $member->person[0]->email .")."
				: ". El intento de enviar por correo la nueva contraseña no ha funcionado. Avisa al administrador del banco de tiempo.";
		} else {
			  $message .= " y el valor nuevo es: $password" ;
		}
		PageView::getInstance()->setMessage($message);
	}

}