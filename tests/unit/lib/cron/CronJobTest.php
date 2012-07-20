<?php

require_once dirname(__FILE__) . "/../../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class CronJobTest extends PHPUnit_Framework_TestCase {

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testIsDue() {
		$executor = $this->getMockForAbstractClass("CronJobExecutor");
		$policy = $this->getMockForAbstractClass("CronJobPolicy");
		$policy->expects($this->once())->method("isDue");

		$job = new CronJob("DUMMY", $executor, $policy);
		$job->isDue();
	}

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testRun() {
		$executor = $this->getMockForAbstractClass("CronJobExecutor");
		$executor->expects($this->once())->method("execute");
		$policy = $this->getMockForAbstractClass("CronJobPolicy");

		$job = new CronJob("DUMMY", $executor, $policy);
		$job->run();
	}

}