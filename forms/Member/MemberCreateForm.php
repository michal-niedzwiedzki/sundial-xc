<?php

final class MemberCreateForm extends Form {
	
	public function __construct() {
		$config = Config::getInstance();

		parent::__construct();
		$this->addElement("text", "member_id", "ID de soci@", array("size" => 10, "maxlength" => 15));
		$this->addElement("text", "password", "Contraseña", array("size" => 10, "maxlength" => 15));
		$this->addElement("select", "member_role", "Tipo de soci@", array("0"=>"Soci@", "1"=>"Gestión", "2"=>"Administrador"));
		$this->addElement("static", null, "Comentario del administrador", null);
		$this->addElement("textarea", "admin_note", null, array("cols"=>45, "rows"=>2, "wrap"=>"soft", "maxlength" => 100));

		$today = getdate();
		$options = array("language"=> "es", "format" => "dFY", "minYear"=>JOIN_YEAR_MINIMUM, "maxYear"=>$today["year"]);
		$this->addElement("date", "join_date",	"Fecha de inscripción", $options);

		$this->addElement("text", "first_name", "Nombre", array("size" => 15, "maxlength" => 20));
		$this->addElement("text", "mid_name", "Primer Apellido", array("size" => 20, "maxlength" => 30));
		$this->addElement("text", "last_name", "Segundo Apellido", array("size" => 20, "maxlength" => 30));
		$this->addElement("text", "fax_number", "DNI o Pasaporte", array("size" => 10, "maxlength" => 20));
		$options = array("language"=> "es", "format" => "dFY", "maxYear"=>$today["year"], "minYear"=>"1900");
		$this->addElement("date", "dob", "Fecha de nacimiento", $options);
		$this->addElement("select", "sex", "Sexo:", array("1"=>"F", "2"=>"M"));

		$this->addElement("text", "email", "Dirección de correo", array("size" => 25, "maxlength" => 40));
		$this->addElement("text", "phone1", "Primer teléfono", array("size" => 20));
		$this->addElement("text", "phone2", "Segundo teléfono", array("size" => 20));
		$this->addElement("text", "address_street1", "Dirección linea 1", array("size" => 25, "maxlength" => 50));
		$this->addElement("text", "address_street2", "Dirección linea 2", array("size" => 25, "maxlength" => 50));

		$frequency = array("0"=>"Nunca", "1"=>"Cada día", "7"=>"Cada semana", "30"=>"Cada mes");
		$this->addElement("select", "email_updates", "Frecuencia de actualizaciones por correo electronico", $frequency);

		$this->addElement('submit', 'btnSubmit', 'Crear Soci@');

		$this->addRule('member_id', 'ID de soci@ obligatorio', 'required');
		$this->addRule('password', 'La contraseña debe tener al menos 7 caracteres', 'minlength', 7);
		$this->addRule('first_name', 'Insertar un nombre', 'required');
		$this->addRule('mid_name', 'Insertar al menos un apellido', 'required');
		$this->addRule('fax_number', 'Insertar identificación', 'required');

		$this->registerRule("verify_unique_member_id", "function", array("memberCreateForm", "verify_unique_member_id"));
		$this->addRule('member_id', 'Este ID de soci@ ya existe', 'verify_unique_member_id');
		$this->registerRule("verify_good_member_id", "function", array("memberCreateForm", "verify_good_member_id"));
		//$this->addRule('member_id', 'Special characters are not allowed', 'verify_good_member_id');
		$this->registerRule("verify_good_password", "function", array("memberCreateForm", "verify_good_password"));
		$this->addRule('password', 'La contraseña debe tener al menos un numero', 'verify_good_password');
		$this->registerRule("verify_no_apostraphes_or_backslashes", "function", array("memberCreateForm", "verify_no_apostraphes_or_backslashes"));
		$this->addRule("password", "La contraseña no puede tener los caracteres ' o \\", "verify_no_apostraphes_or_backslashes");
		$this->registerRule("verify_role_allowed", "function", array("memberCreateForm", "verify_role_allowed"));
		$this->addRule('member_role', 'No tiene permiso para asignar nivel de acceso tan alto', 'verify_role_allowed');
		$this->registerRule("verify_not_future_date", "function", array("memberCreateForm", "verify_not_future_date"));
		$this->addRule('join_date', 'La fecha de inscripción no puede ser una del futuro', 'verify_not_future_date');
		$this->addRule('dob', '¡La fecha no puede ser del futuro!', 'verify_not_future_date');
		$this->registerRule("verify_reasonable_dob", "function", array("memberCreateForm", "verify_reasonable_dob"));
		$this->addRule('dob', 'Un poco joven, ¿no crees?', 'verify_reasonable_dob');
		$this->registerRule("verify_valid_email", "function", array("memberCreateForm", "verify_valid_email"));
		$this->addRule('email', 'Dirección de correo inválida', 'verify_valid_email');
		//$this->registerRule(array("memberCreateForm", "verify_phone_format"),"function",'verify_phone_format');
		//$this->addRule('phone1', 'Formato invalido para número de teléfono', 'verify_phone_format');
		//$this->addRule('phone2', 'Formato invalido para número de teléfono', 'verify_phone_format');
		//$this->addRule('fax', 'Phone format invalid', 'verify_phone_format');

		$current_date = array("Y"=>$today["year"], "F"=>$today["mon"], "d"=>$today["mday"]);
		$defaults = array(
			"password" => cMember::GeneratePassword(),
			"dob" => $current_date,
			"join_date" => $current_date,
			"account_type" => "S",
			"member_role" => "0",
			"email_updates" => $config->legacy->DEFAULT_UPDATE_INTERVAL,
			"address_state_code" => DEFAULT_STATE,
			"address_country" => DEFAULT_COUNTRY
		);
		$this->setDefaults($defaults);
	}

	public static function verify_unique_member_id($name, $value) {
		$member = new cMember();
		return !($member->LoadMember($value, false));
	}

	public static function verify_good_member_id($name, $value) {
		if (ctype_alnum($value)) {
			return true;
		}
		$memberId = ereg_replace("\_","",$value);
		$memberId = ereg_replace("\-","",$memberId);
		$memberId = ereg_replace("\.","",$memberId);
		if (ctype_alnum($memberId)) {
			return true;
		}
	}

	public static function verify_role_allowed($name, $value) {
		$user = cMember::getCurrent();
		return $value <= $user->member_role;
	}

	public static function verify_reasonable_dob($name, $value) {
		global $today;
		$date = $value;
		$date_str = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];

		if ($date_str == $today['year']."/".$today['mon']."/".$today['mday']) {
			return true;
		} elseif ($today['year'] - $date['Y'] < 3) { // A little young to be trading, presumably a mistake
			return false;
		} else {
			return true;
		}
	}

	public static function verify_good_password($name, $value) {
		$i = 0;
		$length = strlen($value);
		while($i < $length) {
			if (ctype_digit($value{$i})) {
				return true;
			}
			$i+=1;
		}
		return false;
	}

	public static function verify_no_apostraphes_or_backslashes($name, $value) {
		return (FALSE === strstr($value, "'") and FALSE === strstr($value,"\\"));
	}

	public static function verify_not_future_date($name, $value) {
		$date = $value;
		$date_str = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
		return strtotime($date_str) <= strtotime("now");
	}

	public static function verify_valid_email($name, $value) {
		if ($value == "")
			return true;
		if (strstr($value,"@") and strstr($value,"."))
			return true;
		else
			return false;
	}

	public static function verify_phone_format($name, $value) {
		$phone = new cPhone_uk($value);
		return (boolean)$phone->prefix;
	}

}