<?php

require_once dirname(__FILE__) . "/../../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

final class ConfigNodeTest extends PHPUnit_Framework_TestCase {

	public function testGetAndSetPrimitives() {
		$node = new ConfigNode();
		$node->i = 1;
		$this->assertEquals(1, $node->i);
		$node->s = "string";
		$this->assertEquals("string", $node->s);
		$node->t = TRUE;
		$this->assertTrue($node->t);
		$node->f = FALSE;
		$this->assertFalse($node->f);
		$node->n = NULL;
		$this->assertNull($node->n);
	}

	public function testGetAndSetArray() {
		$a = array(
			"flat" => 1,
			"nested" => array("null" => NULL)
		);
		$node = new ConfigNode();
		$node->a = $a;
		$this->assertTrue($node->a instanceof ConfigNode);
		$this->assertEquals(1, $node->a->flat);
		$this->assertTrue($node->a->nested instanceof ConfigNode);
		$this->assertNull($node->a->nested->null);
	}

	public function testGetAndSetObject() {
		$nested = new stdClass();
		$nested->null = NULL;
		$o = new stdClass();
		$o->flat = 1;
		$o->nested = $nested;
		$node = new ConfigNode();
		$node->o = $o;
		$this->assertTrue($node->o instanceof ConfigNode);
		$this->assertEquals(1, $node->o->flat);
		$this->assertTrue($node->o->nested instanceof ConfigNode);
		$this->assertNull($node->o->nested->null);
	}

}