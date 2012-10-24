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
	public function reset() {
		$email = HTTPHelper::rq("email");
		$token = HTTPHelper::rq("token");

		$person = cPerson::getByEmail($email);
		$form = new PasswordChangeForm();

		if (!$form->validate()) {
			$this->page->form = $form;
			return;
		}

		if (!$person->password_reset_token or $person->password_reset_token != $token) {
			$this->page->form = $form;
			PageView::getInstance()->setMessage("Token invalid");
			return;
		}

		if ($person->password_reset_expiry < NOW) {
			$this->page->form = $form;
			PageView::getInstance()->setMessage("Expired");
			return;
		}

		$values = $form->exportValues();
		$member = new cMember();
		$member->LoadMember($person->member_id);
		$member->ChangePassword($values["password"]);
		PageView::getInstance()->setMessage("Password changed successfuly");
	}

}