<?php

/**
 * Writer outputting logged messages to the screen
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class LogWriterScreen implements LogWriter {

	private $enabled = FALSE;
	private $startTime = 0;
	private $level = Debug::DEBUG;
	private static $log = array();

	public function __construct($startTime, stdClass $options) {
		$this->startTime = $startTime;
		if (!isset($options->enabled) or !$options->enabled) {
			return;
		}
		isset($options->level) and $this->level = $options->level;
		$this->enabled = TRUE;
	}

	public function log($time, $message, $severity) {
		if ($this->enabled and $severity >= $this->level) {
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