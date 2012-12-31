<?php

final class PasswordController extends Controller {

	/**
	 * @Title "Cambiar contraseña"
	 */
	public function change() {
		$user = cMember::getCurrent();
		$form = new PasswordChangeForm();

		$this->page->form = $form;
		$this->page->fullName = $user->person[0]->first_name . " " . $user->person[0]->mid_name;

		if (!$form->validate()) {
			return;
		}

		$user->ChangePassword($values['new_passwd'])
			? PageView::getInstance()->setMessage('Contraseña cambiada con exito.')
			: PageView::getInstance()->setMessage('Ha occurido un error cambiando la contraseña. Intentalo otra vez mas tarde');

	}

	/**
	 * @Public
	 */
	public function forgot() {
		$form = new PasswordForgottenForm();
		if (!$form->validate()) {
			$this->page->form = $form;
			return;
		}
		$form->freeze();
		$form->process();
		$values = $form->exportValues();
		$email = $values["email"];
		$token = rand(10000, 99999) . rand(10000, 99999) . rand(10000, 99999);
		$link = Link::to("password", "reset", array("email" => $email, "token" => $token));

		$member = cMember::getByEmail($email);
		if (!$member) {
			PageView::getInstance()->setMessage("Unknown email address");
			return;
		}
		$member->forgot_token = $token;
		$member->forgot_expiry = date("Y-m-d H:i:s", time() + 3600);
		$member->SaveMember();

		$message = new EmailMessage(EMAIL_ADMIN, "Password reset request", "You have requested resetting your password. Please click this link to continue: $link");
		$message->to($member);
		$message->save();

		PageView::getInstance()->setMessage("Enviado correo con instrucciones para cambiar tu contrasenia");
	}

	/**
	 * @Public
	 */
	public function reset() {
		$email = HTTPHelper::rq("email");
		$token = HTTPHelper::rq("token");

		$member = cMember::getByEmail($email);
		$form = new PasswordResetForm($email, $token);

		if (!$form->validate()) {
			$this->page->form = $form;
			return;
		}

		if (!$member->forgot_token or $member->forgot_token != $token) {
			$this->page->form = $form;
			PageView::getInstance()->setMessage("Token invalid");
			return;
		}

		if (!$member->forgot_expiry or strtotime($member->forgot_expiry) < time()) {
			$this->page->form = $form;
			PageView::getInstance()->setMessage("Expired");
			return;
		}

		$values = $form->exportValues();
		$member->ChangePassword($values["new_passwd"]);
		PageView::getInstance()->setMessage("Password changed successfuly");
	}

}