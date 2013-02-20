<?php

final class TradeForm extends Form {

	protected static $member;

	public function __construct(cMember $member, $mode, array $namesList, array $categoriesList) {
		parent::__construct();
		self::$member = $member;

		$this->addElement("hidden", "member_id", $member->member_id);
		$this->addElement("hidden", "mode", $mode);
		Config::getInstance()->legacy->MEMBERS_CAN_INVOICE
			? $this->addElement("select", "typ", "Tipo de Transacción", array("Transferencia", "Pago"))
			: $this->addElement("hidden", "typ", 0);
		$this->addElement("select", "member_to", "Transferencia a soci@", $namesList);
		$this->addElement("select", "category", "Categoría", $categoriesList);
		$this->addElement("text", "units", "Cantidad de ". UNITS ."", array("size" => 5, "maxlength" => 10));
		$this->addElement("textarea", "description", "Añadir una descripción del intercambio", array("cols"=>50, "rows"=>4, "wrap"=>"soft"));
		$this->addElement("submit", "btnSubmit", _("(button)Save"));

		$this->registerRule("verify_max255", "function", "verify_max255", "TradeForm");
		$this->registerRule("verify_selection", "function", "verify_selection", "TradeForm");
		$this->registerRule("verify_not_self", "function", "verify_not_self", "TradeForm");
		$this->registerRule("verify_selection", "function", "verify_selection", "TradeForm");
		$this->registerRule("verify_whole_hours", "function", "verify_whole_hours", "TradeForm");
		$this->registerRule("verify_even_minutes", "function", "verify_even_minutes", "TradeForm");
		$this->registerRule("verify_valid_units", "function", "verify_valid_units", "TradeForm");

		$this->addRule("member_to", "No puede hacer una transferencia a si mismo", "verify_not_self");
		$this->addRule("category", "Seleccionar una categoría", "verify_selection");
		$this->addRule("member_to", "Seleccionar un soci@", "verify_selection");
		$this->addRule("description", "Descripción demasiada larga - longitud maxima de 255 caracteres", "verify_max255");
		//$this->addRule("description", "Entrar una descripción", "required");
		if (UNITS == "Horas") {
			$this->addRule("units", "La cantidad de horas tiene que ser una número positivo", "verify_whole_hours");
			$this->addRule("minutes", "Entrar 15, 30, o 45 (o otros divisiones de 3 minutos)", "verify_even_minutes");
		} else {
			$this->addRule("units", "Insertar número positivo.", "verify_valid_units");
		}
	}

	public static function verify_max255($name, $value) {
		return strlen($value) <= 255;
	}

	public static function verify_selection($name, $value) {
		return $value != "0";
	}

	public static function verify_not_self($name, $value) {
		$memberId = substr($value, 0, strpos($value, "?"));
		return $memberId != self::$member->member_id;
	}

	public static function verify_valid_units($name, $value) {
		if ($value <= 0) {
			return FALSE; 
		} elseif ($value * 100 != floor($value * 100)) {
			// allow no more than 2 decimal points
			return FALSE;
		}
		return TRUE;
	}

	public static function verify_even_minutes($name, $minutes) {
		// verifies # of minutes entered represents an evenly
		// divisible fraction w/ no more than 3 decimal points
		return $minutes/60*1000 == floor($minutes/60*1000);
	}

	public static function verify_whole_hours($name, $hours) {
		return abs(floor($hours)) == $hours;
	}

}