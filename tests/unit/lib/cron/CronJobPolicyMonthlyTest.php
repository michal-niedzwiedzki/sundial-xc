<?php

require_once dirname(__FILE__) . "/../../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class CronJobPolicyMonthlyTest extends PHPUnit_Framework_TestCase {

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testIsDue() {
		$policy = new CronJobPolicyMonthly(array("day" => 3, "hour" => 6, "minute" => 20));

		// test one second before, first second, last second, and second after
		$this->assertFalse($policy->isDue(strtotime("3 Jun 2010, 06:19:00")));
		$this->assertTrue($policy->isDue(strtotime("3 Jun 2010, 06:20:00")));
		$this->assertTrue($policy->isDue(strtotime("3 Jun 2010, 06:20:59")));
		$this->assertFalse($policy->isDue(strtotime("3 Jun 2010, 06:21:00")));

		// test the next month
		$this->assertFalse($policy->isDue(strtotime("3 Jul 2010, 06:19:59")));
		$this->assertTrue($policy->isDue(strtotime("3 Jul 2010, 06:20:00")));
		$this->assertTrue($policy->isDue(strtotime("3 Jul 2010, 06:20:59")));
		$this->assertFalse($policy->isDue(strtotime("3 Jul 2010, 06:21:00")));
	}

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testIsDueOnLastDayOfMonth() {
		$policy = new CronJobPolicyMonthly(array("day" => NULL, "hour" => 12, "minute" => 10));

		// test one second before, first second, last second, and second after
		$this->assertFalse($policy->isDue(strtotime("31 Jan 1979, 12:09:00")));
		$this->assertTrue($policy->isDue(strtotime("31 Jan 1979, 12:10:00")));
		$this->assertTrue($policy->isDue(strtotime("31 Jan 1979, 12:10:59")));
		$this->assertFalse($policy->isDue(strtotime("31 Jan 1979, 12:11:00")));

		// test the next month
		$this->assertFalse($policy->isDue(strtotime("28 Feb 1979, 12:09:59")));
		$this->assertTrue($policy->isDue(strtotime("28 Feb 1979, 12:10:00")));
		$this->assertTrue($policy->isDue(strtotime("28 Feb 1979, 12:10:59")));
		$this->assertFalse($policy->isDue(strtotime("28 Feb 1979, 12:11:00")));

		// test leap year
		$this->assertFalse($policy->isDue(strtotime("29 Feb 1980, 12:09:59")));
		$this->assertTrue($policy->isDue(strtotime("29 Feb 1980, 12:10:00")));
		$this->assertTrue($policy->isDue(strtotime("29 Feb 1980, 12:10:59")));
		$this->assertFalse($policy->isDue(strtotime("29 Feb 1980, 12:11:00")));
	}

}