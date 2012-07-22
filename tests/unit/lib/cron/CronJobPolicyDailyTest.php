<?php

require_once dirname(__FILE__) . "/../../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class CronJobPolicyDailyTest extends PHPUnit_Framework_TestCase {

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testIsDue() {
		$policy = new CronJobPolicyDaily(array("hour" => 13, "minute" => 37));

		// test one second before
		$time = strtotime("1 Jan 2012 13:36:59");
		$this->assertFalse($policy->isDue($time));

		// test the first second
		$time = strtotime("1 Jan 2012 13:37:00");
		$this->assertTrue($policy->isDue($time));

		// test the last second
		$time = strtotime("1 Jan 2012 13:37:59");
		$this->assertTrue($policy->isDue($time));

		// test one second after
		$time = strtotime("1 Jan 2012 13:38:00");
		$this->assertFalse($policy->isDue($time));
	}

}