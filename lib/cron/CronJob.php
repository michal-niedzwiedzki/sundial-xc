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
	 * Enviroment to run on
	 * @var string
	 */
	private $env;

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
	 * @param string $env environment identifier
	 * @param CronJobExecutor $executor
	 * @param CronJobPolicy $policy
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function __construct($env, CronJobExecutor $executor, CronJobPolicy $policy) {
		$this->id = NULL;
		$this->env = $env;
		$this->executor = $executor;
		$this->policy = $policy;
		$this->isEnabled = TRUE;
		$this->isRunning = FALSE;
		$this->lastRun = NULL;
	}

	public static function import($id, $env, CronJobExecutor $executor, CronJobPolicy $policy, $isEnabled, $isRunning, $lastRun = NULL) {
		$job = new CronJob();
		$job->id = $id;
		$job->env = $env;
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

	public function run() {
		// TODO: set is_running flag
		// TODO: set last_run date
		$this->executor->execute();
		// TODO: clear is_running_flag
	}

	public function save() {
		// TODO
	}

}