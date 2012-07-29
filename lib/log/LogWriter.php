<?php

interface LogWriter {

	public function __construct($startTime, ConfigNode $config);

	public function log($time, $message, $severity);

	public function done();

}
