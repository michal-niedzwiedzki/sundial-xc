CREATE TABLE member (
	member_id VARCHAR(15) NOT NULL DEFAULT '',
	password VARCHAR(50) NOT NULL DEFAULT '',
	member_role CHAR(1) NOT NULL DEFAULT '',
	security_q VARCHAR(25) DEFAULT NULL,
	security_a VARCHAR(15) DEFAULT NULL,
	status CHAR(1) NOT NULL DEFAULT '',
	member_note VARCHAR(100) DEFAULT NULL,
	admin_note TEXT DEFAULT '',
	join_date DATE NOT NULL DEFAULT '0000-00-00',
	expire_date DATE DEFAULT NULL,
	away_date DATE DEFAULT NULL,
	account_type CHAR(1) NOT NULL DEFAULT '',
	email_updates INT(3) UNSIGNED NOT NULL DEFAULT 0,
	balance DECIMAL(8,2) NOT NULL DEFAULT 0.00,
	confirm_payments INT(1) DEFAULT 0,
	restriction INT(1),
	PRIMARY KEY (member_id)
) ENGINE InnoDB DEFAULT CHARACTER SET utf8;
