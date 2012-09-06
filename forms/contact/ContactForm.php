<?php

final class ContactForm extends Form {

	public function __construct() {
		parent::__construct();
		$this->addElement("text", "name", "Nombre");
		$this->addElement("text", "email", "Correo Electrónico");
		$this->addElement("text", "phone", "Teléfono");
		$this->addElement("textarea", "message", "Su mensaje", array("cols" => 65, "rows" => 10, "wrap" => "soft"));
		$this->addElement("select", "how_heard", "¿Cómo nos conociste?", self::howHeard());
		$this->addElement("submit", "btnSubmit", "Enviar");
		$this->addRule("name", "Has olvidado insertar un nombre", "required");
	}

	public static function howHeard() {
		return array (
			NULL => "(Seleccionar uno)",
			1 => "Medios de comunicación",
			2 => "Blog del banco",
			3 => "Busqueda en internet",
			4 => "Amigos",
			5 => "Negocio local",
			6 => "Otro"
		);
	}

}