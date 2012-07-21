<?php

/**
 * Periodic job execution manager
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class Cron {

	/**
	 * Singleton instance
	 * @var Cron
	 */
	private static $instance;

	/**
	 * List of jobs
	 * @var CronJob[]
	 */
	private $jobs = array();

	/**
	 * Constructor
	 *
	 * Loads jobs for given environment from database.
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	private function __construct() {
		$sql = "SELECT * FROM cron ORDER BY coalesce(last_run, 0), id";
		$rows = PDOHelper::fetchAll($sql, array("env" => $env));
		foreach ($rows as $row) {
			$this->jobs[] = CronJob::import(
				$row["id"],
				CronJobExecutor::get($row["executor"], json_encode($row["executor_settings"])),
				CronJobPolicy::get($row["policy"], json_decode($row["policy_settings"])),
				(boolean)$row["is_enabled"],
				(boolean)$row["is_running"],
				strtotime($row["last_run"])
			);
		}
	}

	/**
	 * Return singleton instance
	 *
	 * @return Cron
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function get() {
		self::$instance or self::$instance = new Cron();
		return self::$instance;
	}

	/**
	 * Add job to the list
	 *
	 * @param CronJob $job
	 * @return Cron
	 */
	public function addJob(CronJob $job) {
		$this->jobs[] = $job;
		return $this;
	}

	/**
	 * Find and run next job that is due
	 *
	 * @return boolean
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function runNextJob() {
		foreach ($this->jobs as $job) {
			if ($job->isEnabled() and !$job->isRunning() and $job->isDue()) {
				return $job->run();
			}
		}
		return FALSE;
	}

	/**
	 * Return all jobs currently flagged as running
	 *
	 * @return CronJob[]
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function getRunningJobs() {
		$jobs = array();
		foreach ($this->jobs as $job) {
			$job->isRunning() and $jobs[] = $job;
		}
		return $jobs;
	}

}