<?php

/**
 * @TableName "users"
 * @PrimaryKey "id"
 */
class User {

	const STATE_INACTIVE = "I"; // newly created account, not yet activated
	const STATE_ACTIVE = "A"; // fully operational account
	const STATE_EXPIRED = "E"; // account expired due to not logging in for long time
	const STATE_SUSPENDED = "S"; // account suspended by administrator
	const STATE_DELETED = "D"; // account deleted

	/**
	 * User primary key or NULL when not yet saved
	 * @var int|NULL
	 * @Column "id"
	 */
	public $id;

	/**
	 * Short name
	 * @var string
	 * @Column "name"
	 * @NotNull
	 */
	public $name;

	/**
	 * Full name
	 * @var string
	 * @Column "full_name"
	 * @NotNull
	 */
	public $fullName;

	/**
	 * Email address
	 * @var string
	 * @Column "email"
	 * @NotNull
	 */
	public $email;

	/**
	 * Login name
	 * @var string
	 * @Column "login"
	 * @NotNull
	 */
	public $login;

	/**
	 * Password
	 * @var string
	 * @Column "password"
	 * @NotNull
	 * @Transformation "Sha1Transformation"
	 */
	public $password;

	/**
	 * Password salt
	 * @var int
	 * @Column "salt"
	 * @NotNull
	 */
	public $salt;

	/**
	 * Recent state
	 * @var string
	 * @Column "state"
	 * @NotNull
	 */
	public $state;

	/**
	 * Recent balance in seconds
	 * @var int
	 * @Column "balance"
	 * @NotNull
	 */
	public $balance;

	/**
	 * Registration date
	 * @var int
	 * @Column "created_on"
	 * @NotNull
	 * @Transformation "DateTransformation"
	 */
	public $createdOn;

	/**
	 * Last modification date
	 * @var int
	 * @Column "updated_on"
	 * @NotNull
	 * @Transformation "DateTransformation"
	 */
	public $updateOn;

	/**
	 * Recent logon date
	 * @var int
	 * @Column "last_seen_on"
	 * @NotNull
	 * @Transformation "DateTransformation"
	 */
	public $lastSeenOn;

	public function __construct(array $a = array()) {
		if (empty($a)) {
			return;
		}
		Persistence::revive($a, $this);
	}

	public static function getById($id) {
		$sql = "SELECT * FROM users WHERE id = :id";
		$row = PDOHelper::fetchRow($sql, array("id" => $id));
		if (empty($row)) {
			return NULL;
		}
		return new User($row);
	}

	public function save() {
		$id = Persistence::freeze($this);
		$this->id or $this->id = $id;
		return $id;
	}

}