<?php

require_once dirname(__FILE__) . "/../../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

final class cMemberTest extends PHPUnit_Framework_TestCase {

	public function testGetByEmail() {
		$email = microtime(TRUE) . "@test.com";
		$member1 = UsersMother::create(array("email" => $email));
		$member2 = cMember::getByEmail($email);
		$this->assertEquals($member1, $member2);
	}

}