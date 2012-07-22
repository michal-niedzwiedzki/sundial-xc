INSERT INTO cron (policy, policy_settings, executor, executor_settings) VALUES
	('CronJobPolicyDaily', '{hour:8,minute:0}', 'EmailListingUpdatesExecutor', '{interval:1}'),
	('CronJobPolicyWeekly', '{offset:0,hour:8,minute:0}', 'EmailListingUpdatesExecutor', '{interval:7}'),
	('CronJobPolicyMonthly', '{day:1,hour:8,minute:0}', 'EmailListingUpdatesExecutor', '{interval:31}');