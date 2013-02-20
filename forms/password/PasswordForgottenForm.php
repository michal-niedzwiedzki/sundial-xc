<?php

final class PasswordForgottenForm extends Form {

	public function __construct() {
		parent::__construct();
		$this->addElement("text", "email", _("(field)Email"), array("size" => 30, "maxlength" => 60));
		$this->addElement("submit", "btnSubmit", _("(button)Reset password"));
	}

}