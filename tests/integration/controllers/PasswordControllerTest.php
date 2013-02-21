<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

require_once ROOT_DIR . "/controllers/PasswordController.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class PasswordControllerTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		parent::setUp();
	}

	/**
	 * Test reporting forgotten password
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testForgot() {
		$member = UsersMother::create();
		$email = $member->getEmail();

		// mock request data
		HTTPHelper::mockPost(array(
			"csrf" => CSRF,
			"email" => $email,
			"btnSubmit" => "submit",
		));

		// assert that token not set
		$this->assertNull($member->forgot_token);
		$this->assertNull($member->forgot_expiry);

		// run controller action
		$controller = new PasswordController();
		$controller->forgot();

		// test if token set
		$member = cMember::getByEmail($email);
		$this->assertNotNull($member->forgot_token);
		$this->assertNotNull($member->forgot_expiry);

		// test if email message created
		// FIXME
	}

	public function testReset() {
		$member = UsersMother::create();
		$email = $member->getEmail();
		$token = "1234567890";
		$password = "NewPassword";
		$member->forgot_token = $token;
		$member->forgot_expiry = date("Y-m-d H:i:s", time() + 10);
		$member->SaveMember();

		// mock request data
		HTTPHelper::mockPost(array(
			"csrf" => CSRF,
			"email" => $email,
			"token" => $token,
			"new_passwd" => $password,
			"rpt_passwd" => $password,
			"btnSubmit" => "submit",
		));

		// assert that token set correctly
		$this->assertEquals($token, $member->forgot_token);
		$this->assertNotNull($member->forgot_expiry);

		// run controller action
		$controller = new PasswordController();
		$controller->reset();

		// test if token set
		$member = cMember::getByEmail($email);
		$this->assertEquals(sha1($password), $member->password);
		$this->assertNull($member->forgot_token);
		$this->assertNull($member->forgot_expiry);
	}

}