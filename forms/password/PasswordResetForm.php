<?php

final class PasswordResetForm extends Form {

	const MIN_LENGTH = 7;
	const MIN_DIGITS = 1;

	public function __construct($email, $token) {
		parent::__construct();
		$this->addElement("password", "new_passwd", "Contraseña nueva");
		$this->addElement("password", "rpt_passwd", "Repite la contraseña nueva");
		$this->addElement("hidden", "email", $email);
		$this->addElement("hidden", "token", $token);
		$this->addElement("submit", "btnSubmit", "Cambiar contraseña");
		$this->addRule("new_passwd", "Insertar una contraseña nueva", "required");
		$this->addRule("rpt_passwd", "Debe insertar la contraseña nueva otra vez", "required");
		$this->addRule("new_passwd", "La contraseña tiene menos de 7 caracteres", "minlength", 7);
		$this->registerRule("verifyPasswordsEqual", "function", "verifyPasswordsEqual", $this);
		$this->addRule("new_passwd", "Las contraseñas nuevas no son iguales", "verifyPasswordsEqual");
		$this->registerRule("verifyStrongPassword", "function", "verifyStrongPassword", $this);
		$this->addRule("new_passwd", "La nueva contraseña debe tener al menos un numero", "verifyStrongPassword");
	}

	public function verifyStrongPassword($value) {
		return strlen($value) >= self::MIN_LENGTH;
	}

	public function verifyPasswordsEqual() {
		return $this->getElementValue("new_passwd") == $this->getElementValue("rpt_passwd");
	}

}