<?php

require_once dirname(__FILE__) . "/../../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class CronJobPolicyWeeklyTest extends PHPUnit_Framework_TestCase {

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testIsDue() {
		$policy = new CronJobPolicyWeekly(array("offset" => 3 /* Wednesday */, "hour" => 12, "minute" => 10));

		// test one second before, first second, last second, and second after
		$this->assertFalse($policy->isDue(strtotime("31 Jan 1979, 12:09:59")));
		$this->assertTrue($policy->isDue(strtotime("31 Jan 1979, 12:10:00")));
		$this->assertTrue($policy->isDue(strtotime("31 Jan 1979, 12:10:59")));
		$this->assertFalse($policy->isDue(strtotime("31 Jan 1979, 12:11:00")));

		// test next 6 days
		$this->assertFalse($policy->isDue(strtotime("1 Feb 1979, 12:10:00")));
		$this->assertFalse($policy->isDue(strtotime("2 Feb 1979, 12:10:00")));
		$this->assertFalse($policy->isDue(strtotime("3 Feb 1979, 12:10:00")));
		$this->assertFalse($policy->isDue(strtotime("4 Feb 1979, 12:10:00")));
		$this->assertFalse($policy->isDue(strtotime("5 Feb 1979, 12:10:00")));
		$this->assertFalse($policy->isDue(strtotime("6 Feb 1979, 12:10:00")));

		// test Wednesday next week
		$this->assertFalse($policy->isDue(strtotime("7 Feb 1979, 12:09:59")));
		$this->assertTrue($policy->isDue(strtotime("7 Feb 1979, 12:10:00")));
		$this->assertTrue($policy->isDue(strtotime("7 Feb 1979, 12:10:59")));
		$this->assertFalse($policy->isDue(strtotime("7 Feb 1979, 12:11:00")));
	}

}