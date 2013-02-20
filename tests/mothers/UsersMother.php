<?php

final class UsersMother {

	public static function instantiate() {
		$user = new User();
		$user->login = "U_" . (microtime(TRUE) - 1343345530);
		$user->email = $user->login . "@test.com";
		$user->name = "Tester";
		$user->fullName = "Anonymous Tester";
		$user->password = User::TEST_PASSWORD;
		return $user;
	}

	public static function regular() {
		$user = self::instantiate();
		$user->save();
		return $user;
	}

	public static function admin() {
		$user = self::instantiate();
		$user->isAdmin = TRUE;
		$user->save();
		return $user;
	}

}