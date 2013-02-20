<?php

final class LoginForm extends Form {

	public function __construct() {
		parent::__construct();
		$this->addElement("text", "username", "Id de socio");
		$this->addElement("password", "password", "Contraseña");
		$this->addElement("submit", "btnSubmit", "Entrar");
		$this->registerRule("verifyUsernameExists", "function", array($this, "verifyUsernameExists"));
		$this->addRule("username", "Invalid username", "vverifyUsernameExists");
		$this->registerRule("verifyPassword", "function", "verifyPassword", $this);
		$this->addRule("password", "Invalid password", "verifyPassword");
	}

	public function verifyUsernameExists($name, $value) {
		return (boolean)User::getByLogin($value);
	}

}