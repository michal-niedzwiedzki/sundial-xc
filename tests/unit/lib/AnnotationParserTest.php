<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

final class AnnotationParserTest extends PHPUnit_Framework_TestCase {

	const ANNOTATED_CLASS = "
		/**
		 * @String \"foo\"
		 * @Int 1
		 * @Array [\"dog\", \"cat\", \"hamster\"]
		 * @Object {\"dog\": \"Azor\", \"cat\": \"Filemon\", \"hamster\": \"Kubuś\"}
		 * @False false
		 * @True true
		 * @Default
		 */
		class _TestAnnotatedClass { }
	";

	private $reflection;

	public function setUp() {
		parent::setUp();
		class_exists("_TestAnnotatedClass") or eval(self::ANNOTATED_CLASS);
		$this->reflection = new ReflectionClass("_TestAnnotatedClass");
	}

	public function testParseString() {
		$out = AnnotationParser::get($this->reflection, "String");
		$this->assertSame("foo", $out);
		$this->assertTrue(is_string($out));
	}

	public function testParseInt() {
		$out = AnnotationParser::get($this->reflection, "Int");
		$this->assertSame(1, $out);
		$this->assertTrue(is_int($out));
	}

	public function testParseArray() {
		$out = AnnotationParser::get($this->reflection, "Array");
		$this->assertSame(array("dog", "cat", "hamster"), $out);
		$this->assertTrue(is_array($out));
	}

	public function testParseObject() {
		$o = new stdClass();
		$o->dog = "Azor";
		$o->cat = "Filemon";
		$o->hamster = "Kubuś";
		$out = AnnotationParser::get($this->reflection, "Object");
		$this->assertEquals($o, $out);
		$this->assertTrue(is_object($out));
	}

	public function testParseFalse() {
		$out = AnnotationParser::get($this->reflection, "False");
		$this->assertSame(FALSE, $out);
		$this->assertTrue(is_bool($out));
	}

	public function testParseTrue() {
		$out = AnnotationParser::get($this->reflection, "True");
		$this->assertSame(TRUE, $out);
		$this->assertTrue(is_bool($out));
	}

	public function testParseDefault() {
		$out = AnnotationParser::get($this->reflection, "Default");
		$this->assertSame(TRUE, $out);
		$this->assertTrue(is_bool($out));
	}

}