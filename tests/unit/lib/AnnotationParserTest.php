<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @String "foo"
 * @Int 1
 * @Array ["dog", "cat", "hamster"]
 * @Object {"dog": "Azor", "cat": "Filemon", "hamster": "Kubuś"}
 * @False false
 * @True true
 * @Default
 */
final class AnnotatedMock1 { }

/**
 * @MultipleAnnotation 1
 * @MultipleAnnotation 2
 * @SingleAnnotation
 */
final class AnnotatedMock2 { }

final class AnnotationParserTest extends PHPUnit_Framework_TestCase {

	public function testGet() {
		$reflection = new ReflectionClass("AnnotatedMock1");
		// parse string
		$out = AnnotationParser::get($reflection, "String");
		$this->assertSame("foo", $out);
		$this->assertTrue(is_string($out));
		// parse int
		$out = AnnotationParser::get($reflection, "Int");
		$this->assertSame(1, $out);
		$this->assertTrue(is_int($out));
		// parse array
		$out = AnnotationParser::get($reflection, "Array");
		$this->assertSame(array("dog", "cat", "hamster"), $out);
		$this->assertTrue(is_array($out));
		// parse object
		$o = new stdClass();
		$o->dog = "Azor";
		$o->cat = "Filemon";
		$o->hamster = "Kubuś";
		$out = AnnotationParser::get($reflection, "Object");
		$this->assertEquals($o, $out);
		$this->assertTrue(is_object($out));
		// parse false
		$out = AnnotationParser::get($reflection, "False");
		$this->assertSame(FALSE, $out);
		$this->assertTrue(is_bool($out));
		// parse true
		$out = AnnotationParser::get($reflection, "True");
		$this->assertSame(TRUE, $out);
		$this->assertTrue(is_bool($out));
		// parse default
		$out = AnnotationParser::get($reflection, "Default");
		$this->assertSame(TRUE, $out);
		$this->assertTrue(is_bool($out));
	}

	public function testGetAll() {
		$reflection = new ReflectionClass("AnnotatedMock2");
		// parse multiple annotations
		$out = AnnotationParser::getAll($reflection, "MultipleAnnotation");
		$this->assertSame(array(1, 2), $out);
		$this->assertTrue(is_array($out));
		// parse single annotation
		$out = AnnotationParser::getAll($reflection, "SingleAnnotation");
		$this->assertSame(array(TRUE), $out);
		$this->assertTrue(is_array($out));
	}

}