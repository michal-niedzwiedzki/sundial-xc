<?php

class UserAccountLocal extends UserAccount {

	public function setPassword($password) {
		if (!$this->password and !$this->token) {
			$this->token = rand(100, 999);
		}
		$this->password = sha1($password . $this->token);
		return $this;
	}

	public function setToken($token) {
		$this->token = $token;
	}

	public function authenticate(UserAccount $account) {
		return $this->id == $account->id
			and $this->password == $account->password
			and $this->token == $account->token;
	}

}