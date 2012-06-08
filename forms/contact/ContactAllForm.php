<?php

final class ContactAllForm extends Form {
	
	public function __construct() {
		parent::__construct();
		$this->addElement("text", "subject", "Asunto", array("size" => 30, "maxlength" => 50));
		$this->addElement("textarea", "message", "Mensaje", array("cols"=>65, "rows"=>10, "wrap"=>"soft"));
		$this->addElement("submit", "btnSubmit", "Enviar");
		$this->addRule("subject", "El asunto es obligatorio", "required");
		$this->addRule("message", "No has incluido el mensaje", "required");
	}

}