<?php

final class LoginController extends Controller {

	/**
	 * @Public
	 */
	public function index() {
		// logout logic
		if (HTTPHelper::rq("action") === "logout") {
			cMember::getCurrent()->Logout();
			return;
		}

		// authenticate and log in
		$redir_url = HTTPHelper::post("location");
		$redir_url or $redir_url = "member_profile.php";
		$user = trim(HTTPHelper::post("user"));
		$pass = trim(HTTPHelper::post("pass"));
		if (!$user) {
			cError::getInstance()->Error("Inserta un nombre de usuario para entrar.");
		} elseif (!$pass) {
			cError::getInstance()->Error("No puede entrar sin contraseÃ±a. Si ha olvidado su contraseÃ±a puede pedir de nosotros una contraseÃnu±a eva");
		} else {
			cMember::getCurrent()->Login($user, $pass);
		}
		include "redirect.php";
	}

	/**
	 * @Public
	 * @Page "login/index.phtml"
	 */
	public function redirect() {
		$this->page->requestUri = HTTPHelper::session("REQUEST_URI");
	}

}