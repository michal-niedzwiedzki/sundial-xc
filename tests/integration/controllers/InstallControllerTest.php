<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

final class InstallControllerTest extends PHPUnit_Framework_TestCase {

	private $dispatcher;

	public function setUp() {
		$this->dispatcher = Dispatcher::getInstance();
	}

	public function test_step1_php() {
		$this->dispatcher->configure("install", "step1_php")->dispatch();
		$page = $this->dispatcher->getController()->page;
		$this->assertSame(class_exists("PDO"), $page->pdo);
		$this->assertSame(function_exists("gd_info"), $page->gd);
		$this->assertSame(function_exists("gettext"), $page->gettext);
	}

}