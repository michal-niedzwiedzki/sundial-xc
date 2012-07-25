CREATE TABLE logins (
	member_id VARCHAR(15) NOT NULL DEFAULT '',
	total_failed MEDIUMINT(6) UNSIGNED NOT NULL DEFAULT 0,
	consecutive_failures MEDIUMINT(3) UNSIGNED NOT NULL DEFAULT 0,
	last_failed_date TIMESTAMP NOT NULL,
	last_success_date TIMESTAMP NOT NULL DEFAULT '00000000000000',
	PRIMARY KEY (member_id)
) ENGINE InnoDB DEFAULT CHARACTER SET utf8;
