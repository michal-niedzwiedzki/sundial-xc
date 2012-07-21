CREATE TABLE email_messages (
	`id`				INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	`from`				VARCHAR(80) NOT NULL,
	`subject`			VARCHAR(80) NOT NULL,
	`body`				TEXT NOT NULL,
	`is_processed`		INT(1) UNSIGNED NOT NULL DEFAULT 0,
	`created_on`		DATETIME DEFAULT NULL
) ENGINE InnoDB DEFAULT CHARSET utf8;

CREATE TABLE email_recipients (
	`id`				INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	`message_id`		INT UNSIGNED REFERENCES email_messages (id) ON DELETE CASCADE,
	`user_id`			INT NOT NULL,
	`name`				VARCHAR(80) NOT NULL,
	`address`			VARCHAR(80) NOT NULL,
	`field`				CHAR(3) NOT NULL,
	`status`			INT(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE InnoDB DEFAULT CHARSET utf8;

CREATE INDEX email_recipients_message_id_idx ON email_recipients (message_id);
