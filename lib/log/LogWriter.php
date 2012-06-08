<?php

interface LogWriter {

	public function __construct($startTime, stdClass $options);

	public function log($time, $message, $severity);

	public function done();

}
