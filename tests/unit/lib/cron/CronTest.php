<?php

require_once dirname(__FILE__) . "/../../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class CronTest extends PHPUnit_Framework_TestCase {

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testGet() {
		$this->assertTrue(Cron::getInstance() instanceof Cron);
	}

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testRunNextJob() {
		    $executor = $this->getMockForAbstractClass("CronJobExecutor");
		    $executor->expects($this->once())->method("execute");
		    $policy = $this->getMockForAbstractClass("CronJobPolicy");
		    $policy->expects($this->once())->method("isDue")->will($this->returnValue(TRUE));

			// prepare cron with one job
			$job = new CronJob($executor, $policy);
		    $cron = Cron::getInstance();
		    $cron->purgeAllJobs();
			$cron->addJob($job);

			// expect policy to allow runnign executor
        	$cron->runNextJob();
	}

}