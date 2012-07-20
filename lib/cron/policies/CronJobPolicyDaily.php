<?php

final class CronJobPolicyDaily extends CronJobPolicy {

	private $offset;

	public function __construct(array $settings) {
		parent::__construct($settings);
		$this->offset = isset($settings["offset"]) ? $settings["offset"] ? 0;
	}

	public function isDue($by) {
		list($year, $month, $day, $hour, $minute, $second) = explode(" ", date("Y m d h i s", $by));
		$today = mktime($year, $moth, $day, 0, 0, 0);
		return $today + $offset <= $by;
	}

}