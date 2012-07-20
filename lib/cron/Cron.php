<?php

/**
 * Periodic job execution manager
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class Cron {

	/**
	 * Pool of Cron instances
	 * @var Cron[]
	 */
	private static $instances = array();

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
	 * @param string $env environment identifier
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	private function __construct($env) {
		$sql = "SELECT * FROM cron WHERE env = :env ORDER BY coalesce(last_run, 0), id";
		$rows = PDOHelper::fetchAll($sql, array("env" => $env));
		foreach ($rows as $row) {
			$this->jobs[] = CronJob::import(
				$row["id"],
				$row["env"],
				CronJobPolicy::get($row["policy"], json_decode($row["policy_settings"])),
				(boolean)$row["is_enabled"],
				(boolean)$row["is_running"],
				strtotime($row["last_run"])
			);
		}
	}

	/**
	 * Return Cron instance for given environment
	 *
	 * @param string $env environment identifier
	 * @return Cron
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function get($env) {
		isset(self::$instances[$env]) or self::$instances[$env] = new Cron($env);
		return self::$instances[$env];
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