<?php

require_once dirname(__FILE__) . "/../../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

final class LinkTest extends PHPUnit_Framework_TestCase {

	private $base;

	public function setUp() {
		parent::setUp();
		$this->base = Config::getInstance()->server->base;
	}

	public function testTo() {
		$expected = "{$this->base}/home_index.php?a=1&b=2&c=3";
		$actual = Link::to("home", "index", array("a" => 1, "b" => 2, "c" => 3));
		$this->assertSame($expected, $actual);
	}

}