<?php

final class Debug {

	const NONE = 0;
	const DEBUG = 1;
	const INFO = 2;
	const WARNING = 3;
	const ERROR = 4;

	private static $instance;

	private $startTime = 0;
	private $writers = array();

	private function __construct(stdClass $writers) {
		$this->startTime = microtime(TRUE);
		foreach ($writers as $className => $options) {
			$this->writers[] = new $className($this->startTime, $options);
		}
	}

	public static function getInstance() {
		self::$instance or self::$instance = new Debug(Config::getInstance()->log);
		return self::$instance;
	}

	public static function log($message, $severity) {
		$time = microtime(TRUE);
		$debug = self::getInstance();
		foreach ($debug->writers as $writer) {
			$writer->log($time, $message, $severity);
		}
	}

	public static function dump(array $a) {
		self::log(print_r($a, TRUE), self::DEBUG);
	}

}
