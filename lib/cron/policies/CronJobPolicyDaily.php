<?php

final class CronJobPolicyDaily extends CronJobPolicy {

	private $hour;
	private $minute;

	public function __construct(array $settings) {
		parent::__construct($settings);
		$this->hour = isset($settings["hour"]) ? $settings["hour"] : 3;
		$this->minute = isset($settings["minute"]) ? $settings["minute"] : 0;
	}

	public function isDue($by) {
		list($year, $month, $day, $hour, $minute, $second) = explode(" ", date("Y m d h i s", $by));
		return mktime($this->hour, $this->minute, 0, $month, $day, $year) <= $by;
	}

}