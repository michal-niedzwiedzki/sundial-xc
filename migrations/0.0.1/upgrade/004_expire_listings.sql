INSERT INTO cron (policy, policy_settings, executor, executor_settings) VALUES
	('CronJobPolicyDaily', '{hour:8,minute:10}', 'ExpireListingsExecutor', '{}');
