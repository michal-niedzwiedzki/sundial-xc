<?php

final class PasswordChangeForm extends Form {

	public function __construct() {
		parent::__construct();
		$options = array("size" => 10, "maxlength" => 15);
		$this->addElement("password", "old_passwd", "Contraseña vieja", $options);
		$this->addElement("password", "new_passwd", "Contraseña nueva", $options);
		$this->addElement("password", "rpt_passwd", "Repite la contraseña nueva", $options);
		$this->addElement("submit", "btnSubmit", "Cambiar contraseña");
		$this->addRule("old_passwd", "Insertar la contraseña actual", "required");
		$this->addRule("new_passwd", "Insertar una contraseña nueva", "required");
		$this->addRule("rpt_passwd", "Debe insertar la contraseña nueva otra vez", "required");
		$this->addRule("new_passwd", "La contraseña tiene menos de 7 caracteres", "minlength", 7);
		$this->registerRule("verify_passwords_equal", "function", array($this, "verify_passwords_equal"));
		$this->addRule("new_passwd", "Las contraseñas nuevas no son iguales", "verify_passwords_equal");
		$this->registerRule("verify_old_password", "function", "verify_old_password", $this);
		$this->addRule("old_passwd", "La contraseña vieja is incorrecta", "verify_old_password");
		$this->registerRule("verify_good_password", "function", "verify_good_password", $this);
		$this->addRule("new_passwd", "La nueva contraseña debe tener al menos un numero", "verify_good_password");
	}

	public function verify_old_password($name, $value) {
		return cMember::getCurrent()->ValidatePassword($value);
	}

	public function verify_good_password($name, $value) {
		$i = 0;
		$length = strlen($value);
		while ($i < $length) {
			if (ctype_digit($value{$i}))
				return TRUE;
			$i+=1;
		}
		return FALSE;
	}

	public function verify_passwords_equal() {
		return $this->getElementValue('new_passwd') == $this->getElementValue('rpt_passwd');
	}

}