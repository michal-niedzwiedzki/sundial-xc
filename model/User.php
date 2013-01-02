<?php

class User {

	const TABLE_NAME = "users";

	const STATE_INACTIVE = "I"; // newly created account, not yet activated
	const STATE_ACTIVE = "A"; // fully operational account
	const STATE_EXPIRED = "E"; // account expired due to not logging in for long time
	const STATE_SUSPENDED = "S"; // account suspended by administrator
	const STATE_DELETED = "D"; // account deleted

	/**
	 * User primary key or NULL when not yet saved
	 * @var int|NULL
	 */
	protected $id;

	/**
	 * Short name
	 * @var string
	 */
	protected $name;

	/**
	 * Full name
	 * @var string
	 */
	protected $fullName;

	/**
	 * Registration date
	 * @var int
	 */
	protected $createdOn;

	/**
	 * Last modification date
	 * @var int
	 */
	protected $updateOn;

	/**
	 * Recent logon date
	 * @var int
	 */
	protected $lastSeenOn;

	/**
	 * Recent state
	 * @var string
	 */
	protected $state;

	/**
	 * Recent balance in seconds
	 * @var int
	 */
	protected $balance;

	/**
	 * User accounts
	 * @var UserAccount[]
	 */
	protected $accounts = array();

	public function __construct(array $a = array()) {
		if (empty($a)) {
			return;
		}
		$this->id = $a["id"];
		$this->name = $a["name"];
		$this->fullName = $a["full_name"];
		$this->createdOn = strtotime($a["created_on"]);
		$this->updatedOn = strtotime($a["updated_on"]);
		$this->lastSeenOn = strtotime($a["last_seen_on"]);
		$this->state = $a["state"];
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
		$this->createdOn or $this->createdOn = time();
		$this->updatedOn = time();
		$this->state or $this->state = self::STATE_INACTIVE;
		$this->balance or $this->balance = 0;
		$row = array(
			"name" => $this->name,
			"full_name" => $this->fullName,
			"created_on" => date("Y-m-d H:i:s", $this->createdOn),
			"updated_on" => date("Y-m-d H:i:s", $this->updatedOn),
			"last_seen_on" => $this->lastSeenOn ? date("Y-m-d H:i:s", $this->lastSeenOn) : NULL,
			"state" => $this->state,
			"balance" => (int)$this->balance,
		);
		return $this->id
			? PDOHelper::update(self::TABLE_NAME, $row, "id = :id", array("id" => $this->id))
			: ($this->id = PDOHelper::insert(self::TABLE_NAME, $row));
	}

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getFullName() {
		return $this->fullName;
	}

	public function setFullName($fullName) {
		$this->fullName = $fullName;
		return $this;
	}

	public function getAccount($account) {
		$this->accounts or $this->accounts = UserAccount::getByUserId($this->id);
		if (isset($this->accounts[$account]) {
			return $this->accounts[$account];
		}
	}

	public function getAllAccounts() {
		$this->accounts or $this->accounts = UserAccount::getByUserId($this->id);
		return $this->accounts;
	}

}