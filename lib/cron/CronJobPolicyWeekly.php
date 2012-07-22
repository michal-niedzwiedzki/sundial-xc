<?php

/**
 * Policy to activate cron job weekly at given hour
 *
 * Parameters in settings array:
 * "offset" - day of the week (0 is Sunday), default 0
 * "hour" - hour of activation in 24h format, default 4am
 * "minute" - minute of activation, default 0
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class CronJobPolicyWeekly extends CronJobPolicy {

	private $offset;
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
		$this->offset = isset($settings["offset"]) ? $settings["offset"] : 0;
		$this->hour = isset($settings["hour"]) ? $settings["hour"] : 4;
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
		if (date("w", $by) != $this->offset) {
			return FALSE;
		}
		list($year, $month, $day, $hour, $minute) = explode(" ", date("Y m d H i", $by));
		$beginning = mktime($this->hour, $this->minute, 0, $month, $day, $year);
		$end = $beginning + 60; // one minute later
		return $by >= $beginning and $by < $end;
	}

	public function getMinimumInterval() {
		return 604800; // 1 week
	}

}