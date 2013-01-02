CREATE TABLE email_messages (
	`id`				INT UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT 'Message serial number',
	`from`				VARCHAR(80) NOT NULL COMMENT 'Sender address',
	`subject`			VARCHAR(80) NOT NULL COMMENT 'Message subject',
	`body`				TEXT NOT NULL COMMENT 'Message body',
	`is_processed`		INT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Message processed flag',
	`created_on`		DATETIME DEFAULT NULL COMMENT 'Date of creation'
) ENGINE InnoDB DEFAULT CHARSET utf8;

CREATE TABLE email_recipients (
	`id`				INT UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT 'Message recipient serial number',
	`message_id`		INT UNSIGNED REFERENCES email_messages (id) ON DELETE CASCADE COMMENT 'Message serial number',
	`user_id`			INT NOT NULL COMMENT '',
	`name`				VARCHAR(80) NOT NULL COMMENT 'Message recipient name',
	`address`			VARCHAR(80) NOT NULL COMMENT 'Message recipient address',
	`field`				CHAR(3) NOT NULL COMMENT 'Recipient type (to:, cc:, bcc:)',
	`status`			INT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Delivery status, see EmailRecipient::STATUS_*'
) ENGINE InnoDB DEFAULT CHARSET utf8;

CREATE INDEX email_recipients_message_id_idx ON email_recipients (message_id);
