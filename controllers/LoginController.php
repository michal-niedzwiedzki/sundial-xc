<?php

final class LoginController extends Controller {

	/**
	 * @Public
	 */
	public function index() {
		$redirUrl = HTTPHelper::post("location");
		$redirUrl or $redirUrl = "member_profile.php";
		$this->page->redirUrl = $redirUrl;
		$this->page->csrf = CSRF;

		// check if already logged in
		if (cMember::IsLoggedOn()) {
			HTTPHelper::redirect($redirUrl);
			return;
		}

		// authenticate and log in
		$user = trim(HTTPHelper::post("user"));
		$pass = trim(HTTPHelper::post("pass"));
		if (!$user) {
			cError::getInstance()->Error("Inserta un nombre de usuario para entrar.");
		} elseif (!$pass) {
			cError::getInstance()->Error("No puede entrar sin contraseña. Si ha olvidado su contraseña puede pedir de nosotros una contrase�nu�a eva");
		}

		if (cMember::getCurrent()->Login($user, $pass)) {
			HTTPHelper::redirect($redirUrl);
		}
	}

	/**
	 * @Public
	 * @Page "login/index"
	 */
	public function logout() {
		cMember::getCurrent()->Logout();
		HTTPHelper::redirect("login.php");
	}

	/**
	 * @Public
	 * @Page "login/index"
	 */
	public function redirect() {
		$this->page->redirUrl = HTTPHelper::session("REQUEST_URI");
		$this->page->csrf = CSRF;
	}

	/**
	 * @Public
	 */
	public function forgot_password() {
		$form = new LoginForgotPasswordForm();
		if (!$form->validate()) {
			$this->page->form = $form;
			return;
		}
		$form->freeze();
		$form->process();
		$values = $form->exportValues();
		$email = $values["email"];
		$token = rand(10000, 99999) . rand(10000, 99999) . rand(10000, 99999);
		$link = Link::to("login", "reset_password", array("email" => $email, "token" => $token));

		$person = cPerson::getByEmail($email);
		$person->password_reset_token = $token;
		$person->SavePerson();

		$member = new cMember();
		$member->LoadMember($person->member_id);

		$message = new EmailMessage(EMAIL_ADMIN, "Password reset request", "You have requested resetting your password. Please click this link to continue: $link");
		$message->to($member);
		$message->save();

		PageView::getInstance()->setMessage("Enviado correo con instrucciones para cambiar tu contrasenia");
	}

}