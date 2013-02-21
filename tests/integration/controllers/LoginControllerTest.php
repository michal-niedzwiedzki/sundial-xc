<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

require_once ROOT_DIR . "/controllers/LoginController.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class LoginControllerTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		parent::setUp();
		User::logOut();
	}

	/**
	 * Test login successful
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testIndexLoginSuccessful() {
		$user = UsersMother::regular();

		// mock request data
		HTTPHelper::mockPost(array(
			"csrf" => CSRF,
			"user" => $user->login,
			"pass" => User::TEST_PASSWORD,
			"submit" => "submit",
		));

		// run controller
		$controller = new LoginController();
		$controller->index();

		// test if user logged in
		$currentUser = User::getCurrent();
		$this->assertTrue(User::isLoggedIn());
		$this->assertTrue(User::getCurrentId() > 0);
		$this->assertTrue($currentUser->id > 0);
		$this->assertSame($user->id, $currentUser->id);
		$this->assertSame($user->id, User::getCurrentId());
		$this->assertSame($user->id, $_SESSION[User::SESSION_KEY]);
	}

	/**
	 * Test login failed due to bad login
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testIndexLoginFailedBadLogin() {
		// mock request data
		HTTPHelper::mockPost(array(
			"csrf" => CSRF,
			"user" => "BAD_LOGIN",
			"pass" => User::TEST_PASSWORD,
			"submit" => "submit",
		));

		// run controller
		$controller = new LoginController();
		$controller->index();

		// test if user logged in
		$this->assertFalse(User::isLoggedIn());
		$this->assertNull(User::getCurrent());
		$this->assertNull(User::getCurrentId());
	}

	/**
	 * Test login failed due to bad password
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testIndexLoginFailedBadPassword() {
		$user = UsersMother::regular();

		// mock request data
		HTTPHelper::mockPost(array(
			"csrf" => CSRF,
			"user" => $user->login,
			"pass" => User::TEST_PASSWORD . "_BAD_PASSWORD",
			"submit" => "submit",
		));

		// run controller
		$controller = new LoginController();
		$controller->index();

		// test if user logged in
		$this->assertFalse(User::isLoggedIn());
		$this->assertNull(User::getCurrent());
		$this->assertNull(User::getCurrentId());
	}

}