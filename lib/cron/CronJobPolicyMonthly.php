<?php

/**
 * Policy to activate cron job monthly at given hour
 *
 * Parameters in settings array:
 * "day" - day of the month (1 to 31, NULL for last day of the month), default 1
 * "hour" - hour of activation in 24h format, default 5am
 * "minute" - minute of activation, default 0
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class CronJobPolicyMonthly extends CronJobPolicy {

	private $day;
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
		$this->day = array_key_exists("day", $settings) ? $settings["day"] : 1; // isset is misreporting on NULLs
		$this->hour = isset($settings["hour"]) ? $settings["hour"] : 5;
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
		list($year, $month, $day, $hour, $minute, $numOfDays) = explode(" ", date("Y m d H i t", $by));
		$daysOffset = $this->day ? $this->day : $numOfDays;
		if ($day != $daysOffset) {
			return FALSE;
		}
		$beginning = mktime($this->hour, $this->minute, 0, $month, $day, $year);
		$end = $beginning + 60; // one minute later
		return $by >= $beginning and $by < $end;
	}

	public function getMinimumInterval() {
		return 2419200; // 28 days - the shortest month
	}

}