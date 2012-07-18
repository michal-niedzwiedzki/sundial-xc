<?php

require_once dirname(__FILE__) . "/../../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

final class ConfigTest extends PHPUnit_Framework_TestCase {

	public function testGetInstance() {
		$this->assertTrue(Config::getInstance() instanceof Config);
	}

	public function testMock() {
		$mock = Config::mock();
		$mock->test = "yes";
		$config = Config::getInstance();
		$this->assertEquals("yes", $config->test);
	}

	public function testLoad() {
		$file = tempnam(sys_get_temp_dir(), __FILE__);
		file_put_contents($file, "{\"test\":\"yes\"}");
		$mock = Config::mock();
		$mock->load($file);
		$this->assertEquals("yes", $mock->test);
	}

	public function testSave() {
		$file = tempnam(sys_get_temp_dir(), __FILE__);
		$mock = Config::mock();
		$mock->test = "yes";
		$mock->save($file);
		$this->assertTrue(file_exists($file));
		$this->assertEquals("{\"test\":\"yes\"}", trim(file_get_contents($file)));
		unlink($file);
	}

}