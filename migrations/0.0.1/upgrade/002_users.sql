CREATE TABLE users (
	id					INT UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT 'User serial number (primary key)',
	name				VARCHAR(50) NOT NULL COMMENT 'Short name',
	full_name			VARCHAR(150) NOT NULL COMMENT 'Full name',
	email				VARCHAR(100) NOT NULL COMMENT 'Email address',
	login				VARCHAR(100) NOT NULL COMMENT 'User name',
	password			VARCHAR(40) NOT NULL COMMENT 'Password',
	salt				CHAR(3) NOT NULL COMMENT 'Password salt',
	token				INT NULL COMMENT 'Password reset token',
	token_expires_on	DATETIME NULL COMMENT 'Date of token expiry',
	state				CHAR(1) COMMENT 'User state, see User::STATE_*',
	balance				INT NOT NULL DEFAULT 0 COMMENT 'Calculated balance',
	is_admin			INT(1) NOT NULL DEFAULT 0 COMMENT 'Administrator flag',
	created_on			DATETIME NOT NULL COMMENT 'Date of creation',
	updated_on			DATETIME NOT NULL COMMENT 'Date of last modification',
	last_seen_on		DATETIME NULL COMMENT 'Date of last logon'
) ENGINE InnoDB DEFAULT CHARSET utf8;

CREATE UNIQUE INDEX users_login_unq ON users (login);
CREATE UNIQUE INDEX users_email_unq ON users (email);

-- copy existing members
INSERT INTO users (name, full_name, email, login, password, salt, state, is_admin, created_on, updated_on)
	SELECT p.first_name, CONCAT(p.first_name, p.mid_name, p.last_name), p.email, m.member_id, m.password, "", "I", 0, m.join_date, m.join_date
	FROM member AS m
	LEFT JOIN person AS p ON p.member_id = m.member_id;

-- update statuses
UPDATE user SET state = "A" WHERE login = (SELECT member_id FROM member WHERE status = "A");
UPDATE user SET state = "S" WHERE login = (SELECT member_id FROM member WHERE status = "L");

-- update admin flag
UPDATE user SET is_admin = 1 WHERE login = (SELECT member_id FROM member WHERE member_role = 1);
