<?php

final class MemberCreateForm extends Form {

	public function __construct() {
		$config = Config::getInstance();
		$today = getdate();

		parent::__construct();
		$this->addElement("text", "login", "Login", array("size" => 10, "maxlength" => 15));
		$this->addElement("text", "password", "Contraseña", array("size" => 10, "maxlength" => 15));
		$this->addElement("checkbox", "admin", "Administrador");

		$this->addElement("text", "name", "Nobre o puesto", array("size" => 15, "maxlength" => 20));
		$this->addElement("text", "surname", "Apellidos", array("size" => 20, "maxlength" => 30));

		$options = array("language"=> "es", "format" => "dFY", "maxYear" => $today["year"], "minYear" => "1900");
		$this->addElement("date", "dob", "Fecha de nacimiento", $options);

		$this->addElement("text", "email", "Dirección de correo", array("size" => 25, "maxlength" => 40));
		$this->addElement("text", "phone", "Teléfono", array("size" => 20));
		$this->addElement("text", "address1", "Dirección linea 1", array("size" => 25, "maxlength" => 50));
		$this->addElement("text", "address2", "Dirección linea 2", array("size" => 25, "maxlength" => 50));
		$this->addElement("text", "post_code", "Codigo postal", array("size" => 10, "maxlength" => 10));
		$this->addElement("text", "city", "Ciudad");

		$this->addElement('submit', 'btnSubmit', 'Crear Socio');

		$this->addRule('login', 'ID de soci@ obligatorio', 'required');
		$this->addRule('password', 'La contraseña debe tener al menos 7 caracteres', 'minlength', 7);
		$this->addRule('name', 'Insertar un nombre', 'required');
		$this->addRule('surname', 'Insertar al menos un apellido', 'required');

		$this->registerRule("verifyLoginUnique", "function", "verifyLoginUnique", $this);
		$this->addRule('member_id', 'Este ID de soci@ ya existe', 'verifyLoginUnique');
		$this->registerRule("varifyLoginCharacters", "function", "varifyLoginCharacters", $this);
		//$this->addRule('member_id', 'Special characters are not allowed', 'varifyLoginCharacters');
		$this->registerRule("verifyPasswordStrength", "function", "verifyPasswordStrength", $this);
		$this->addRule('password', 'La contraseña debe tener al menos un numero', 'verifyPasswordStrength');
		$this->registerRule("verify_no_apostraphes_or_backslashes", "function", "verify_no_apostraphes_or_backslashes", $this);
		$this->addRule("password", "La contraseña no puede tener los caracteres ' o \\", "verify_no_apostraphes_or_backslashes");
		$this->registerRule("verify_role_allowed", "function", "verify_role_allowed", $this);
		$this->addRule('member_role', 'No tiene permiso para asignar nivel de acceso tan alto', 'verify_role_allowed');
		$this->registerRule("verify_not_future_date", "function", "verify_not_future_date", $this);
		$this->addRule('join_date', 'La fecha de inscripción no puede ser una del futuro', 'verify_not_future_date');
		$this->addRule('dob', '¡La fecha no puede ser del futuro!', 'verify_not_future_date');
		$this->registerRule("verify_reasonable_dob", "function", "verify_reasonable_dob", $this);
		$this->addRule('dob', 'Un poco joven, ¿no crees?', 'verify_reasonable_dob');
		$this->registerRule("verify_valid_email", "function", "verify_valid_email", $this);
		$this->addRule('email', 'Dirección de correo inválida', 'verify_valid_email');
		//$this->registerRule(array("memberCreateForm", "verify_phone_format"),"function",'verify_phone_format');
		//$this->addRule('phone1', 'Formato invalido para número de teléfono', 'verify_phone_format');
		//$this->addRule('phone2', 'Formato invalido para número de teléfono', 'verify_phone_format');
		//$this->addRule('fax', 'Phone format invalid', 'verify_phone_format');

		$current_date = array("Y" => $today["year"], "F" => $today["mon"], "d" => $today["mday"]);
		$defaults = array(
			"password" => rand(1000, 9999) . rand(1000, 9999),
			"dob" => $current_date,
			"join_date" => $current_date,
			"email_updates" => $config->legacy->DEFAULT_UPDATE_INTERVAL,
		);
		$this->setDefaults($defaults);
	}

	public function verifyLoginUnique($login) {
		return (boolean)User::getByLogin($login);
	}

	public function varifyLoginCharacters($value) {
		return ctype_alnum($value);
	}

	public function verify_role_allowed($value) {
		$user = cMember::getCurrent();
		return $value <= $user->member_role;
	}

	public function verify_reasonable_dob($value) {
		$dob = strtotime($value['Y'] . '/' . $value['F'] . '/' . $value['d']);
		return $dob > time() - 3600 * 24 * 365 * 3; // 3 years
	}

	public function verifyPasswordStrength($password) {
		return strlen($password) > 6;
	}

	public function verify_no_apostraphes_or_backslashes($value) {
		return (FALSE === strstr($value, "'") and FALSE === strstr($value,"\\"));
	}

	public function verify_not_future_date($value) {
		$date = $value;
		$date_str = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
		return strtotime($date_str) <= strtotime("now");
	}

	public function verify_valid_email($value) {
		if ($value == "")
			return true;
		if (strstr($value,"@") and strstr($value,"."))
			return true;
		else
			return false;
	}

	public function verify_phone_format($value) {
		$phone = new cPhone_uk($value);
		return (boolean)$phone->prefix;
	}

}