CREATE TABLE cron (
	`id`					INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	`policy`				VARCHAR(80) NOT NULL,
	`policy_settings`		TEXT NOT NULL,
	`executor`				VARCHAR(80) NOT NULL,
	`executor_settings`		TEXT NOT NULL,
	`is_enabled`			INT(1) UNSIGNED NOT NULL DEFAULT 1,
	`is_running`			INT(1) UNSIGNED NOT NULL DEFAULT 0,
	`last_run`				DATETIME DEFAULT NULL
) ENGINE InnoDB DEFAULT CHARSET utf8;
