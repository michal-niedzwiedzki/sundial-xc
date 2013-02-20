<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class LoginControllerTest extends PHPUnit_Framework_TestCase {

	private $user;
	private $dispatcher;

	public function setUp() {
		parent::setUp();
		User::logOut();
		$this->user = UsersMother::regular();
		$this->dispatcher = Dispatcher::getInstance();
		$_SESSION = array();
	}

	/**
	 * Test login successful
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testIndex_successful() {
		// mock request data
		HTTPHelper::mockPost(array(
			"csrf" => CSRF,
			"user" => $this->user->getId(),
			"pass" => cMember::DEFAULT_PASSWORD,
			"submit" => "submit",
		));

		// run controller
		Dispatcher::getInstance()->configure("login", "index")->dispatch();

		// test if user logged in
		$currentUser = User::getCurrent();
		$this->assertTrue(User::isLoggedIn());
		$this->assertTrue(User::getCurrentId() > 0);
		$this->assertTrue($currentUser->id > 0);
		$this->assertSame($this->user->id, $currentUser->id);
		$this->assertSame($this->user->id, User::getCurrentId());
		$this->assertSame($this->user->id, $_SESSION[User::SESSION_KEY]);
	}

	/**
	 * Test login failed
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testIndex_failed() {
		// mock request data
		HTTPHelper::mockPost(array(
			"csrf" => CSRF,
			"user" => $this->user->getId(),
			"pass" => "BAD_" . cMember::DEFAULT_PASSWORD,
			"submit" => "submit",
		));

		// run controller
		Dispatcher::getInstance()->configure("login", "index")->dispatch();

		// test if user logged in
		$current = cMember::getCurrent();
		$this->assertEquals("", $current->getId());
		$this->assertFalse(array_key_exists("user_login", $_SESSION));
	}

}