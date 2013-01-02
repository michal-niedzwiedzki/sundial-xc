<?php

class User {

	const TABLE_NAME = "users";

	const STATUS_INACTIVE = "I"; // newly created account, not yet activated
	const STATUS_ACTIVE = "A"; // fully operational account
	const STATUS_EXPIRED = "E"; // account expired due to not logging in for long time
	const STATUS_SUSPENDED = "S"; // account suspended by administrator
	const STATUS_DELETED = "D"; // account deleted

	/**
	 * User primary key or NULL when not yet saved
	 * @var int|NULL
	 */
	protected $id;

	/**
	 * Full name
	 * @var string
	 */
	protected $name;

	/**
	 * Registration date
	 * @var int
	 */
	protected $createdOn;

	/**
	 * Recent logon date
	 * @var int
	 */
	protected $lastSeenOn;

	/**
	 * Recent status
	 * @var string
	 */
	protected $status;

	/**
	 * Recent balance in seconds
	 * @var int
	 */
	protected $balance;

	public function __construct(array $a = array()) {
		if (empty($a)) {
			return;
		}
		$this->id = $a["id"];
		$this->name = $a["name"];
		$this->createdOn = strtotime($a["created_on"]);
		$this->updatedOn = strtotime($a["updated_on"]);
		$this->lastSeenOn = strtotime($a["last_seen_on"]);
		$this->status = $a["status"];
		$this->balance = $a["balance"];
	}

	public static function getById($id) {
		$sql = "SELECT * FROM users WHERE id = :id";
		$row = PDOHelper::fetchRow($sql, array("id" => $id));
		if (empty($row)) {
			return NULL;
		}
		return new User($row);
	}

	public static function getByAccount($account) {
		$sql = "SELECT * FROM users WHERE id = (SELECT user_id FROM accounts WHERE account = :account)";
		$row = PDOHelper::fetchRow($sql, array("account" => $account));
		if (empty($row)) {
			return NULL;
		}
		return new User($row);
	}

	public function save() {
		$row = array(
			"name" => $this->name,
			"updated_on" => date("Y-m-d H:i:s"),
			"status" => $this->status ? $this->status : self::STATUS_INACTIVE
		);
		return $this->id
			? PDOHelper::update(self::TABLE_NAME, $row, "id = :id", array("id" => $id))
			: ($this->id = PDOHelper::insert(self::TABLE_NAME, $row));
	}

	public function getId() {
		return $this->id;
	}

}