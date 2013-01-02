<?php

/**
 * Configuration container
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class Config extends ConfigNode {

	const CONFIG_DIR = "var/config";

	/**
	 * Singleton
	 * @var Config
	 */
	private static $instance;

	/**
	 * Filters for processing raw config object
	 * @var ConfigFilter[]
	 */
	private $filters = array();

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
		self::$instance or self::$instance = new Config();
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

	/**
	 * Return config file pathname
	 *
	 * @return string
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function getDefaultConfigFile() {
		return ROOT_DIR . DIRECTORY_SEPARATOR . self::CONFIG_DIR . DIRECTORY_SEPARATOR . ENV . ".json";
	}

	/**
	 * Add config filter
	 *
	 * @param ConfigFilter $filter
	 * @return Config
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function addFilter(ConfigFilter $filter) {
		$this->filters[] = $filter;
		return $this;
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
		foreach ($this->filters as $filter) {
			$filter->process($object);
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