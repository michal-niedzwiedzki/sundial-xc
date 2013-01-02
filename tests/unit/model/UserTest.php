<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class UserTest extends PHPUnit_Framework_TestCase {

	private $userNames = array();

	private function generateUserName() {
		$userName = "U_" . microtime(TRUE);
		$userNames[] = $userName;
		return $userName;
	}

	public function tearDown() {
		foreach ($this->userNames as $userName) {
			PDOHelper::delete(User::TABLE_NAME, array("name" => $userName));
		}
		parent::tearDown();
	}

	/**
	 * Test user creation
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testSaveNewUser() {
		$name = $this->generateUserName();
		$user = new User(array("name" => $name, "full_name" => $name));
		$this->assertTrue((boolean)$user->save());
		$this->assertTrue($user->getId() > 0);
	}

	/**
	 * Test user update
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testSaveExistingUser() {
		$name = $this->generateUserName();
		$user = new User(array("name" => $name, "full_name" => $name));
		$this->assertTrue((boolean)$user->save());
		$user->setFullName($name . "_new");
		$this->assertTrue((boolean)$user->save());
	}

}