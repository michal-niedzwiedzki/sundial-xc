CREATE TABLE users (
	id				INT UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT 'User serial number (primary key)',
	name			VARCHAR(50) NOT NULL COMMENT 'Short name',
	full_name		VARCHAR(150) NOT NULL COMMENT 'Full name',
	state			CHAR(1) COMMENT 'User state, see User::STATE_*',
	balance			INT NOT NULL COMMENT 'Calculated balance',
	created_on		DATETIME NOT NULL COMMENT 'Date of creation',
	updated_on		DATETIME NOT NULL COMMENT 'Date of last modification',
	last_seen_on	DATETIME NULL COMMENT 'Date of last logon',
	tmp				VARCHAR(200)
) ENGINE InnoDB DEFAULT CHARSET utf8;

INSERT INTO users (state, tmp)
	SELECT "A", member_id FROM member;
