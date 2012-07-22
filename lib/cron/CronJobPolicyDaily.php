<?php

/**
 * Policy to activate cron job daily at given hour
 *
 * Parameters in settings array:
 * "hour" - hour of activation in 24h format, default 3am
 * "minute" - minute of activation, default 0
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class CronJobPolicyDaily extends CronJobPolicy {

	private $hour;
	private $minute;

	/**
	 * Constructor
	 *
	 * @param array $settings
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function __construct(array $settings) {
		parent::__construct($settings);
		$this->hour = isset($settings["hour"]) ? $settings["hour"] : 3;
		$this->minute = isset($settings["minute"]) ? $settings["minute"] : 0;
	}

	/**
	 * Activation logic
	 *
	 * @param int $by
	 * @return boolean
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function isDue($by) {
		list($year, $month, $day, $hour, $minute, $second) = explode(" ", date("Y m d H i s", $by));
		$beginning = mktime($this->hour, $this->minute, 0, $month, $day, $year);
		$end = $beginning + 60; // one minute later
		return $by >= $beginning and $by < $end;
	}

	public function getMinimumInterval() {
		return 86400; // 1 day
	}

}