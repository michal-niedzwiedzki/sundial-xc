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
		$this->assertTrue(Cron::get("DUMMY") instanceof Cron);
	}

}