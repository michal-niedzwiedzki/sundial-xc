<?php

final class LoginForm extends Form {

	public function __construct() {
		parent::__construct();
		$this->addElement("text", "username", _("(field)User name"));
		$this->addElement("password", "password", _("(field)Password"));
		$this->addElement("submit", "btnSubmit", _("(button)Log in"));
		$this->registerRule("verifyUsernameExists", "function", array($this, "verifyUsernameExists"));
		$this->addRule("username", "Invalid username", "verifyUsernameExists");
		$this->registerRule("verifyPassword", "function", "verifyPassword", $this);
		$this->addRule("password", "Invalid password", "verifyPassword");
	}

	public function verifyUsernameExists($name, $value) {
		return cMember::getCurrent()->ValidatePassword($value);
	}

}