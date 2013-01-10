CREATE TABLE users (
	id				INT UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT 'User serial number (primary key)',
	name			VARCHAR(50) NOT NULL COMMENT 'Short name',
	full_name		VARCHAR(150) NOT NULL COMMENT 'Full name',
	email			VARCHAR(100) NOT NULL COMMENT 'Email address',
	login			VARCHAR(100) NOT NULL COMMENT 'User name',
	password		VARCHAR(40) NOT NULL COMMENT 'Password',
	salt			CHAR(3) NOT NULL COMMENT 'Password salt',
	state			CHAR(1) COMMENT 'User state, see User::STATE_*',
	balance			INT NOT NULL DEFAULT 0 COMMENT 'Calculated balance',
	created_on		DATETIME NOT NULL COMMENT 'Date of creation',
	updated_on		DATETIME NOT NULL COMMENT 'Date of last modification',
	last_seen_on	DATETIME NULL COMMENT 'Date of last logon',
	tmp				VARCHAR(200)
) ENGINE InnoDB DEFAULT CHARSET utf8;

CREATE UNIQUE INDEX users_login_unq ON users (login);
CREATE UNIQUE INDEX users_email_unq ON users (email);

INSERT INTO users (name, full_name, email, login, password, salt, state, created_on, updated_on)
	SELECT p.first_name, CONCAT(p.first_name, p.mid_name, p.last_name), p.email, m.member_id, m.password, "", m.status, m.join_date, m.join_date
	FROM member AS m
	LEFT JOIN person AS p ON p.member_id = m.member_id;
