CREATE TABLE cron (
	id					INT UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT 'Cron job serial number',
	policy				VARCHAR(80) NOT NULL COMMENT 'Policy class name',
	policy_settings		TEXT NOT NULL COMMENT 'JSON hash of parameters for policy constructor',
	executor			VARCHAR(80) NOT NULL COMMENT 'Executor class name',
	executor_settings	TEXT NOT NULL COMMENT 'JSON hash of parameters for executor constructor',
	is_enabled			INT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Flag indicating if job is enabled',
	is_running			INT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Flag indicating if job is running at present',
	last_run			DATETIME DEFAULT NULL COMMENT 'Date of last run'
) ENGINE InnoDB DEFAULT CHARSET utf8;
