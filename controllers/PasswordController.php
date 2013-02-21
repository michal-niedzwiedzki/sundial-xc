<?php

final class PasswordController extends Controller {

	/**
	 * @Title "Cambiar contraseña"
	 */
	public function change() {
		$currentUser =Userr::getCurrent();
		$form = new PasswordChangeForm();

		$this->view->form = $form;
		$this->view->fullName = $user->fullName;

		if (!$form->validate()) {
			return;
		}
		$values = $form->process();

		$currentUser->password = $values["new_password"];
		$currentUser->save()
			? PageView::getInstance()->setMessage('Contraseña cambiada con exito.')
			: PageView::getInstance()->setMessage('Ha occurido un error cambiando la contraseña. Intentalo otra vez mas tarde');
	}

	/**
	 * @Public
	 */
	public function forgot() {
		$form = new PasswordForgottenForm();
		if (!$form->validate()) {
			$this->view->form = $form;
			return;
		}
		$values = $form->process();

		$email = $values["email"];
		$token = rand(1000, 9999) . rand(1000, 9999);
		$link = Link::to("password", "reset", array("email" => $email, "token" => $token));

		$user = User::getByEmail($email);
		if (!$user) {
			PageView::getInstance()->setMessage("Unknown email address");
			return;
		}
		$user->token = $token;
		$user->tokenExpiresOn = time() + 3600;
		$user->save();

		$message = new EmailMessage(EMAIL_ADMIN, "Password reset request", "You have requested resetting your password. Please click this link to continue: $link");
		$message->to($user);
		$message->save();

		PageView::getInstance()->setMessage("Enviado correo con instrucciones para cambiar tu contrasenia");
	}

	/**
	 * @Public
	 */
	public function reset() {
		$email = HTTPHelper::rq("email");
		$token = HTTPHelper::rq("token");

		$user = User::getByEmail($email);
		$form = new PasswordResetForm($email, $token);

		if (!$form->validate()) {
			$this->view->form = $form;
			return;
		}
		$values = $form->process();

		if (!$user->token or $user->token != $token) {
			$this->view->form = $form;
			PageView::getInstance()->setMessage("Invalid password reset request");
			return;
		}

		if (!$user->tokenExpiresOn or $user->tokenExpiresOn < time()) {
			$this->view->form = $form;
			PageView::getInstance()->setMessage("Password reset request has expired");
			return;
		}

		$user->password = $values["new_passwd"];
		$user->save();
		PageView::getInstance()->setMessage("Password changed successfuly");
	}

}