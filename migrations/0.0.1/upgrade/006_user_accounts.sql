CREATE TABLE user_accounts (
	account			VARCHAR(200) PRIMARY KEY COMMENT 'Account serial number (primary key)',
	user_id			INT UNSIGNED NOT NULL REFERENCES users (id) ON DELETE CASCADE COMMENT 'User serial number (foreign key)',
	class_name		VARCHAR(100) NOT NULL COMMENT 'Class representing record',
	password		TEXT COMMENT 'Hashed password (if present)',
	uri				TEXT COMMENT 'Unique location identifier (if applicable)',
	token			TEXT COMMENT 'Authentication token (if present)',
	json			TEXT COMMENT 'Additional data in JSON format (if present)',
	state			CHAR(1) COMMENT 'Account state, see UserAccount::STATE_*',
	created_on		DATETIME NOT NULL DEFAULT current_time COMMENT 'Date of creation',
	updated_on		DATETIME NOT NULL DEFAULT current_time COMMENT 'Date of last modification'
) ENGINE InnoDB DEFAULT CHARSET utf8;

CREATE INDEX user_accounts_user_id_idx ON user_accounts (user_id);

INSERT INTO user_accounts (account, user_id, class_name, password, state)
	SELECT u.tmp, u.id, "UserAccountLocal", m.password, "A" FROM users AS u
	INNER JOIN member AS m ON m.member_idi = u.tmp;

ALTER TABLE users DROP COLUMN tmp;
OPTIMIZE TABLE users;
