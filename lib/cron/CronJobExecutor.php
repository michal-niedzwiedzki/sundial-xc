<?php

abstract class CronJobExecutor {

	protected $settings;

	public function __construct(array $settings) {
		$this->settings = $settings;
	}

	abstract public function execute();

	public static function get($className, array $settings) {
		return new $className($settings);
	}

}