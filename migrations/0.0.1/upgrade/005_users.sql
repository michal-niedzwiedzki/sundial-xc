CREATE TABLE uses (
	id				INT UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT 'User serial number (primary key)',
	state			CHAR(1) COMMENT 'User state, see User::STATE_*',
	created_on		DATETIME NOT NULL DEFAULT current_time COMMENT 'Date of creation',
	updated_on		DATETIME NOT NULL DEFAYLT current_time COMMENT 'Date of last modification',
	tmp				VARCHAR(200)
) ENGINE InnoDB DEFAULT CHARSET utf8;

INSERT INTO users (state, tmp)
	SELECT "A", member_id FROM member;
