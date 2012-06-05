<?php

final class LogWriterFile implements LogWriter {

	private $file;
	private $level = Debug::DEBUG;
	private $format = "{severity}\t@{time}\t+{duration}\t{message}";

	public function __construct($startTime, stdClass $options) {
		$this->startTime = $startTime;
		if (!isset($options->file)) {
			throw new Exception("Missing mandatory config param 'file'");
		}
		$this->file = realpath(ROOT_DIR . "/" . $options->file);
		if (!is_writable($this->file)) {
			throw new Exception("File '{$this->file}' not writable");
		}
		isset($options->level) and $this->level = $options->level;
		isset($options->format) and $this->format = $options->format;
	}

	public function log($time, $message, $severity) {
		if ($severity >= $this->level) {
			$replacements = array(
				"{time}" => $time,
				"{duration}" => $time - $this->startTime,
				"{message}" => trim($message),
				"{severity}" => $severity
			);
			$message = trim(str_replace(array_keys($replacements), array_values($replacements), $this->format));
			file_put_contents($this->file, "{$message}\n", FILE_APPEND);
		}
	}

	public function done() { }

}
