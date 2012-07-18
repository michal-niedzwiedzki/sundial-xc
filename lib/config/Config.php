<?php

/**
 * Configuration container
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class Config extends ConfigNode {

	/**
	 * Singleton
	 * @var Config
	 */
	private static $instance;

	/**
	 * Constructor
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	private function __construct() { }

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
			$file = self::getConfigFile();
			self::$instance = new Config();
			self::$instance->load($file);
		}
		return self::$instance;
	}

	/**
	 * Mocks config with empty instance
	 *
	 * @return Config
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function mock() {
		self::$instance = new Config();
		return self::$instance;
	}

	public static function getConfigFile() {
		return ROOT_DIR . "/config/" . ENV . ".json";
	}

	/**
	 * Load configuration properties from file
	 *
	 * @param string $file containing valid JSON string
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function load($file) {
		if (!is_readable($file)) {
			throw new ConfigException("File '$file' could not be read");
		}
		$object = json_decode(trim(file_get_contents($file)));
		if (!is_object($object)) {
			throw new ConfigException("File '$file' does not contain valid JSON string");
		}
		$this->import($object);
	}

	/**
	 * Save configuration properties into file
	 *
	 * @param string $file to be saved into
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function save($file) {
		if (!is_writable($file)) {
			throw new ConfigException("File '$file' could not be open for writing");
		}
		file_put_contents($file, json_encode($this->export()));
	}

}