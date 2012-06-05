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

	public function reset() {
		include ROOT_DIR . "/legacy/password_reset.php";
	}

}