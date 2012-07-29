<?php

/**
 * Email message with list of recipients
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class EmailMessage {

	const TABLE_EMAILS = "email_messages";
	const TABLE_RECIPIENTS = "email_recipients";

	const STATUS_PENDING = 0;
	const STATUS_SENT = 1;
	const STATUS_ERROR = 2;

	const FIELD_TO = "to";
	const FIELD_CC = "cc";
	const FIELD_bcc = "bcc";

	protected $id;
	protected $from;
	protected $subject;
	protected $body;
	protected $recipients = array();

	/**
	 * Constructor
	 *
	 * @param string $form
	 * @param string $subject
	 * @param string $body
	 */
	public function __construct($from, $subject, $body) {
		$this->from = $from;
		$this->subject = $subject;
		$this->body = $body;
	}

	/**
	 * Return email message saved into database
	 *
	 * @param int $id
	 * @return EmailMessage
	 */
	public static function getById($id) {
		$row = PDOHelper::fetchRow("SELECT * FROM " . self::TABLE_EMAILS . " WHERE id = :id", array("id" => $id));
		$message = new EmailMessage($row["from"], $row["subject"], $row["body"]);
		$message->id = $id;

		// add recipients
		$rows = PDOHelper::fetchAll("SELECT * FROM email_recipients WHERE message_id = :id", array("id" => $id));
		foreach ($rows as $row) {
			$message->addRecipient($row["field"], $row["user_id"], $row["name"], $row["address"], $row["id"], $row["status"]);
		}
		return $message;
	}

	public function getId() {
		return $this->id;
	}

	public function getFrom() {
		return $this->from;
	}

	public function getSubject() {
		return $this->subject;
	}

	public function getBody() {
		return $this->body;
	}

	public function isProcessed() {
		return $this->is_processed;
	}

	public function markAsProcessed() {
		$this->is_processed = TRUE;
	}

	/**
	 * Return recipients of the message
	 *
	 * @param int $status optional filter for recipients, default no filter
	 * @return array
	 */
	public function getRecipients($status = NULL) {
		if (NULL === $status) {
			return $this->recipients;
		}
		$recipients = array();
		foreach ($this->recipients as $address => $recipient) {
			$recipient["status"] === $status and $recipients[$address] = $recipient;
		}
		return $recipients;
	}

	/**
	 * Add recipient
	 *
	 * @param string $field
	 * @param int $userId
	 * @param string $userName
	 * @param string $address
	 * @param int $id primary key of recipient for this particular message, default NULL
	 * @param int $status message delivery status, default self::STATUS_PENDING
	 * @return EmailMessage
	 */
	public function addRecipient($field, $userId, $userName, $address, $id = NULL, $status = self::STATUS_PENDING) {
		$this->recipients[$address] = array(
			"id" => $id,
			"field" => $field,
			"user_id" => $userId,
			"name" => $userName,
			"address" => $address,
			"status" => $status
		);
		return $this;
	}

	/**
	 * Add user as recipient in To: field
	 *
	 * @param cMember $user
	 * @return EmailMessage
	 */
	public function to(cMember $user) {
		$this->addRecipient(self::FIELD_TO, 0, "", $user->person[0]->email);
		return $this;
	}

	/**
	 * Update delivery status for a recipient
	 *
	 * @param string $address
	 * @param int $status
	 */
	public function updateDeliveryStatus($address, $status) {
		$this->recipients[$address]["status"] = $status;
	}

	/**
	 * Save message into database
	 *
	 * Inserts or updates message into database.
	 * Iterates over all recipients and inserts or updates them as well.
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function save() {
		$row = array(
			"from" => $this->from,
			"subject" => $this->subject,
			"body" => (string)$this->body,
			"is_processed" => (int)$this->isProcessed
		);

		// save row into database
		if ($this->id) {
			if (!PDOHelper::update(self::TABLE_EMAILS, $row, "id = :id", array("id" => $this->id))) {
				throw new EmailException("Coult not update email message #{$this->id} in database");
			}
		} else {
			$this->id = PDOHelper::insert(self::TABLE_EMAILS, $row);
			if (!$this->id) {
				throw new EmailException("Could not insert email message into database");
			}
		}

		// transactionally save recipients rows
		PDOHelper::begin();
		foreach ($this->recipients as $address => $recipient) {
			$row = array(
				"message_id" => $this->id,
				"address" => $address,
				"field" => $recipient["field"],
				"user_id" => $recipient["user_id"],
				"name" => $recipient["name"],
				"status" => $recipient["status"],
			);
			// save row into database
			if ($recipient["id"]) {
				$out = PDOHelper::update(self::TABLE_RECIPIENTS, $row, "id = :id", array("id" => $recipient["id"]));
			} else {
				$out = PDOHelper::insert(self::TABLE_RECIPIENTS, $row);
				$this->recipients[$address]["id"] = $out;
			}
			if (!$out) {
				PDOHelper::rollBack();
				throw new EmailException("Could not store recipient {$row['address']} into database");
			}
		}
		PDOHelper::commit();
	}

}