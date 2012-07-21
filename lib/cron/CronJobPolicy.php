<?php

abstract class CronJobPolicy {

	protected $settings;

	public function __construct(array $settings) {
		$this->settings = $settings;
	}

	abstract public function isDue($by);

	public static function get($className, array $settings) {
		return new $className($settings);
	}

}