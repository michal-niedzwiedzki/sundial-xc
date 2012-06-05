<?php

final class LogWriterScreen implements LogWriter {

	private $startTime = 0;
	private $level = Debug::DEBUG;
	private static $log = array();

	public function __construct($startTime, stdClass $options) {
		$this->startTime = $startTime;
		isset($options->level) and $this->level = $options->level;
	}

	public function log($time, $message, $severity) {
		if ($severity >= $this->level) {
			self::$log[] = array(
				"time" => $time,
				"duration" => $time - $this->startTime,
				"message" => trim($message),
				"severity" => $severity
			);
		}
	}

	public function done() { }

	public static function getLog() {
		return self::$log;
	}

}
