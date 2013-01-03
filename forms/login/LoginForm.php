<?php

final class LoginForm extends Form {

	public function __construct() {
		parent::__construct();
		$this->addElement("text", "username", "(field)User name");
		$this->addElement("password", "password", "(field)Password");
		$this->addElement("submit", "btnSubmit", "(button)Log in");
		$this->registerRule("verifyUsernameExists", "function", array($this, "verifyUsernameExists"));
		$this->addRule("username", "Invalid username", "verifyUsernameExists");
		$this->registerRule("verifyPassword", "function", "verifyPassword", $this);
		$this->addRule("password", "Invalid password", "verifyPassword");
	}

	public function verifyUsernameExists($name, $value) {
		return cMember::getCurrent()->ValidatePassword($value);
	}

}