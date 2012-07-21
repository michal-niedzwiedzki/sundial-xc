<?php

/**
 * Periodically executed job
 *
 * Each job has its policy defined to give a green light for running.
 * Once run it willl call its executor to do the actual job.
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class CronJob {

	/**
	 * Job identifier
	 * @var int
	 */
	private $id;

	/**
	 * Executor
	 * @var CronJobExecutor
	 */
	private $executor;

	/**
	 * Policy
	 * @var CronJobPolicy
	 */
	private $policy;

	/**
	 * Job enabled flag
	 * @var boolean
	 */
	private $isEnabled;

	/**
	 * Job running flag
	 * @var boolean
	 */
	private $isRunning;

	/**
	 * Last run
	 * @var int
	 */
	private $lastRun;

	/**
	 * Constructor
	 *
	 * @param CronJobExecutor $executor
	 * @param CronJobPolicy $policy
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function __construct(CronJobExecutor $executor, CronJobPolicy $policy) {
		$this->id = NULL;
		$this->executor = $executor;
		$this->policy = $policy;
		$this->isEnabled = TRUE;
		$this->isRunning = FALSE;
		$this->lastRun = NULL;
	}

	public static function import($id, CronJobExecutor $executor, CronJobPolicy $policy, $isEnabled, $isRunning, $lastRun = NULL) {
		$job = new CronJob();
		$job->id = $id;
		$job->policy = $policy;
		$job->isEnabled = $isEnabled;
		$job->isRunning = $isRunning;
		$job->lastRun = $lastRun;
		return $job;
	}

	/**
	 * Return whether the job is flagged as running
	 *
	 * @return boolean
	 */
	public function isRunning() {
		return $this->isRunning;
	}

	/**
	 * Return whether policy allows the job to be run
	 *
	 * @return boolean
	 */
	public function isDue($by = NULL) {
		$by or $by = NOW;
		return $this->policy->isDue($by);
	}

	/**
	 * Run job executor and update database
	 */
	public function run() {
		PDOHelper::update("cron", array("is_running" => 1, "last_run" => "now()", "id = :id", array("id" => $this->id)));
		$this->executor->execute();
		PDOHelper::update("cron", array("is_running" => 0, "id = :id", array("id" => $this->id)));
	}

}