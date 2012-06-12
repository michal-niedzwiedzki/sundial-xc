<?php

/**
 * Configuration container
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class Config {

	/**
	 * Singleton
	 * @var Config
	 */
	private static $instance;

	/**
	 * Constructor
	 *
	 * @param stdClass $object
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	private function __construct(stdClass $object) {
		foreach ($object as $property => $value) {
			$this->$property = $value;
		}
	}

	/**
	 * Return config instance for given environment
	 *
	 * Config file is expected to be in config/ directory and be in JSON format.
	 * File name should be ENV const followed by .json extension.
	 *
	 * @return Config
	 * @throw AssertionFailedException
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function getInstance() {
		if (!self::$instance) {
			$fn = ROOT_DIR . "/config/" . ENV . ".json";
			Assert::fileExists($fn);
			$object = json_decode(trim(file_get_contents($fn)));
			Assert::isObject($object);
			self::$instance = new Config($object);
		}
		return self::$instance;
	}

	/**
	 * Mocks config using passed configuration properties
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function mock(stdClass $object) {
		self::$instance = new Config($object);
	}

}