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
		$this->assertTrue((int)$user->salt > 0);
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

	/**
	 * Test getting single user by id
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
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
		$this->assertSame(sha1("123" . $user->salt), $user->password);
	}

	/**
	 * Test getting collection of users using filter
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testFilter() {
		$name = $this->generateUserName();
		$user = new User(array("login" => $name, "name" => $name, "full_name" => $name, "email" => $name, "password" => "123"));
		$user->save();

		$return = array("login = :login", array("login" => $name));
		$filter = $this->getMock("UserFilter");
		$filter->expects($this->once())
			->method("get")
			->will($this->returnValue($return));

		$users = User::filter($filter);
		$this->assertSame(1, count($users));
		$this->assertTrue($users[0] instanceof User);
		$this->assertSame($name, $users[0]->name);
	}

	/**
	 * Test validation of plain text password against hashed secret
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testValidatePassword() {
		$password = "MyPassword_123";
		$salt = 100;
		$hashed = sha1($password . $salt);

		$user = new User(array("salt" => $salt, "password" => $hashed));
		$this->assertTrue($user->validatePassword($password));
	}

	public function testMakePassword() {
		$password = "MyPassword_123";
		$salt = 100;
		$hashed = User::makePassword($password, $salt);

		$this->assertSame(sha1($password . $salt), $hashed);
	}


}