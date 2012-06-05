<?php

final class LoginController extends Controller {

	/**
	 * @Public
	 */
	public function index() {
		$user = cMember::getCurrent();
		$action = HTTPHelper::post("action");
		$action or $action = HTTPHelper::get("action");
		$action === "logout" and $user->Logout();
		if ($action == "login") {
			$redir_url = HTTPHelper::post("location");
			$user = trim(HTTPHelper::post("user"));
			$pass = trim(HTTPHelper::post("pass"));
			if (!$user) {
				cError::getInstance()->Error("Inserta un nombre de usuario para entrar.");
			} elseif (!$pass) {
				cError::getInstance()->Error("No puede entrar sin contraseña. Si ha olvidado su contraseña puede pedir de nosotros una contraseña nueva.");
			} else {
				cMember::getCurrent()->Login($user, $pass);
			}
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