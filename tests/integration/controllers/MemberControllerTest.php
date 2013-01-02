<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class MemberControllerTest extends PHPUnit_Framework_TestCase {

	private $controller;

	public function setUp() {
		parent::setUp();
		require_once ROOT_DIR . "/controllers/MemberController.php";
		$this->controller = new MemberController();
	}

	/**
	 * Test successful user creation
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testCreate() {
		$memberId = "U_" . time();
		$password = "password123";

		// mock request data
		HTTPHelper::mockPost(array(
			"csrf" => CSRF,
			"member_id" => $memberId,
			"password" => $password,
			"member_role" => 0,
			"admin_note" => "",
			"join_date" => array("d" => 29, "F" => 7, "Y" => 2012),
			"first_name" => "MemberController",
			"mid_name" => $memberId,
			"last_name" => "Test",
			"fax_number" => "1234567890",
			"dob" => array("d" => 31, "F" => 1, "Y" => 1979),
			"sex" => 1,
			"email" => "{$memberId}@mailinator.com",
			"phone1" => "",
			"phone2" => "",
			"address_street1" => "",
			"address_street2" => "",
			"email_updates" => 0,
			"btnSubmit" => "submit",
		));

		// run controller action
		$this->controller->create();

		// test if user created
		$user = new cMember();
		$user->LoadMember($memberId);
		$this->assertEquals($memberId, $user->getId());
		$this->assertEquals(sha1($password), $user->password);
	}

}