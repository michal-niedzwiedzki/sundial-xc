<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * Unit test for class User
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class UserTest extends PHPUnit_Framework_TestCase {

	private function generateUserName() {
		return "U_" . microtime(TRUE);
	}

	/**
	 * Test user creation
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testSaveNewUser() {
		$name = $this->generateUserName();
		$user = new User(array("login" => $name, "name" => $name, "full_name" => $name, "email" => $name, "password" => "123"));
		$this->assertTrue((boolean)$user->save());
		$this->assertTrue($user->id > 0);
	}

	/**
	 * Test user update
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testSaveExistingUser() {
		$name = $this->generateUserName();
		$user = new User(array("login" => $name, "name" => $name, "full_name" => $name, "email" => $name, "password" => "123"));
		$this->assertTrue((boolean)$user->save());
		$user->fullName = $name . "_new";
		$this->assertTrue((boolean)$user->save());
	}

	public function testGetById() {
		$name = $this->generateUserName();
		$user = new User(array("login" => $name, "name" => $name, "full_name" => $name, "email" => $name, "password" => "123"));
		$this->assertTrue((boolean)$user->save());
		$id = $user->id;
		unset($user);

		$user = User::getById($id);
		$this->assertSame($name, $user->login);
		$this->assertSame($name, $user->name);
		$this->assertSame($name, $user->fullName);
		$this->assertSame($name, $user->email);
		$this->assertSame(sha1("123"), $user->password);
	}

}