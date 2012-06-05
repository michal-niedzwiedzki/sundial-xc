<?php

/**
 * Assertion tester
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class Assert {

	private function __construct() { }

	/**
	 * Assert expression is true
	 *
	 * @throw AssertionFailedException
	 */
	public static function true($value) {
		if (!$value) {
			throw new AssertionFailedException("Expected TRUE");
		}
	}

	/**
	 * Assert parameter is scalar
	 *
	 * @throw AssertionFailedException
	 */
	public static function isScalar($scalar) {
		if (!is_scalar($scalar)) {
			throw new AssertionFailedException("Not a scalar");
		}
	}

	/**
	 * Assert parameter is an array
	 *
	 * @throw AssertionFailedException
	 */
	public static function isArray($array) {
		if (!is_array($array)) {
			throw new AssertionFailedException("Not an array");
		}
	}

	/**
	 * Assert parameter is an object
	 *
	 * @throw AssertionFailedException
	 */
	public static function isObject($object) {
		if (!is_object($object)) {
			throw new AssertionFailedException("Not an object");
		}
	}

	/**
	 * Assert array key exists
	 *
	 * @throw AssertionFailedException
	 */
	public static function hasKey($key, $array) {
		if (!is_scalar($key)) {
			throw new AssertionFailedException("Key not scalar");
		}
		if (!is_array($array)) {
			throw new AssertionFailedException("Expected array");
		}
		if (!array_key_exists($key, $array)) {
			throw new AssertionFailedException("Key not present");
		}
	}

	/**
	 * Assert object has public property
	 *
	 * @throw AssertionFailedException
	 */
	public static function hasProperty($property, $object) {
		if (!is_scalar($property)) {
			throw new AssertionFailedException("Property not scalar");
		}
		if (!is_object($object)) {
			throw new AssertionFailedException("Expected object");
		}
		if (!isset($object->$property)) {
			throw new AssertionFailedException("Property not present");
		}
	}

	/**
	 * Assert constant is defined
	 *
	 * @throw AssertionFailedException
	 */
	public static function isDefined($const) {
		if (!defined($const)) {
			throw new AssertionFailedException("Constant not defined");
		}
	}

	/**
	 * Assert constant is not defined
	 *
	 * @throw AssertionFailedException
	 */
	public static function isNotDefined($const) {
		if (defined($const)) {
			throw new AssertionFailedException("Constant defined");
		}
	}

	public static function fileExists($fn, $readable = TRUE, $writable = FALSE) {
		if (!file_exists($fn)) {
			throw new AssertionFailedException("File does not exist");
		}
		if ($readable and !is_readable($fn)) {
			throw new AssertionFailedException("File not readable");
		}
		if ($writable and !is_writable($fn)) {
			throw new AssertionFailedException("File not writable");
		}
	}

}

/**
 * Assertion failure exception
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class AssertionFailedException extends Exception { }