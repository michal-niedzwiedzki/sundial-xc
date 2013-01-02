<?php

class UserAccount {

	const TABLE_NAME = "user_accounts";

	const STATE_INACTIVE = "I"; // newly created account, not yet activated
	const STATE_ACTIVE = "A"; // fully operational account
	const STATE_DELETED = "D"; // account deleted
	const STATE_BANNED = "B"; // account banned

	/**
	 * Account identifier
	 * @var string
	 */
	protected $id;

	/**
	 * User identifier
	 * @var int
	 */
	protected $userId;

	protected $password;
	protected $uri;
	protected $token;

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
	 * Recent state
	 * @var string
	 */
	protected $state;

	protected $user;

	protected $exists = FALSE;

	protected static function import(array $a) {
		$className = $a["class_name"];
		$account = new $className();
		$account->id = $a["id"];
		$account->password = $a["password"];
		$account->uri = $a["uri"];
		$account->token = $a["token"];
		$account->userId = $a["user_id"];
		$account->state = $a["state"];
		$account->createdOn = strtotime($a["created_on"]);
		$account->updatedOn = strtotime($a["updated_on"]);
		$account->exists = TRUE;
		return $account;
	}

	public static function getById($id) {
		$sql = "SELECT * FROM user_accounts WHERE id = :id";
		$row = PDOHelper::fetchRow($sql, array("id" => $id));
		if (empty($row)) {
			return NULL;
		}
		return self::import($row);
	}

	public static function getByUserId($userId) {
		$sql = "SELECT * FROM user_accounts WHERE user_id = :userId";
		$rows = PDOHelper::fetchRow($sql, array("userId" => $userId));
		$accounts = array();
		foreach ($rows as $row) {
			$id = $row["id"];
			$accounts[$id] = self::import($row);
		}
		return $accounts;
	}

	public function save() {
		$this->createdOn or $this->createdOn = time();
		$this->updatedOn = time();
		$this->state or $this->state = self::STATE_INACTIVE;
		$this->balance or $this->balance = 0;
		$row = array(
			"id" => $this->id,
			"user_id" => $this->userId,
			"class_name" => get_class($this),
			"password" => $this->password,
			"uri" => $this->uri,
			"token" => $this->token,
			"state" => $this->state,
			"created_on" => date("Y-m-d H:i:s", $this->createdOn),
			"updated_on" => date("Y-m-d H:i:s", $this->updatedOn),
		);
		$ok = $this->exists
			? PDOHelper::update(self::TABLE_NAME, $row, "id = :id", array("id" => $this->id))
			: PDOHelper::insert(self::TABLE_NAME, $row));
		$this->exists or $this->exists = $ok;
		return $ok;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getUser() {
		$this->user or $this->user = User::getById($this->id);
		return $this->user;
	}

	abstract public function authenticate(UserAccount $account);

}