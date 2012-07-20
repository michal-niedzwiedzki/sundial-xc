<?php

abstract class CronJobExecutor {

	public function __construct() { }

	abstract public function execute();

}