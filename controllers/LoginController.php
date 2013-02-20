<?php

final class LoginController extends Controller {

	/**
	 * @Public
	 */
	public function index() {
		$redirUrl = HTTPHelper::post("location");
		$redirUrl or $redirUrl = "member_profile.php";
		$this->view->redirUrl = $redirUrl;
		$this->view->csrf = CSRF;

		// check if already logged in
		if (User::isLoggedIn()) {
			HTTPHelper::redirectSeeOther($redirUrl);
			return;
		}

		// authenticate and log in
		$login = trim(HTTPHelper::post("user"));
		$pass = trim(HTTPHelper::post("pass"));
		if (!$login) {
			cError::getInstance()->Error("Inserta un nombre de usuario para entrar.");
		} elseif (!$pass) {
			cError::getInstance()->Error("No puede entrar sin contraseña. Si ha olvidado su contraseña puede pedir de nosotros una contrase�nu�a eva");
		}

		$user = User::getByLogin($login);
		if ($user and $user->validatePassword($pass)) {
			HTTPHelper::redirectSeeOther($redirUrl);
		}
	}

	/**
	 * @Public
	 * @Page "login/index"
	 */
	public function logout() {
		User::logOut();
		HTTPHelper::redirectSeeOther(Link::to("login", "index"));
	}

	/**
	 * @Public
	 * @Page "login/index"
	 */
	public function redirect() {
		$this->view->redirUrl = HTTPHelper::session("REQUEST_URI");
		$this->view->csrf = CSRF;
	}

}