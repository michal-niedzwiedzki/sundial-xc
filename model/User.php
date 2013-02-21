<?php

/**
 * User
 *
 * Knows its offers and trades.
 * Can be retrieved from or stored into database.
 * Can create and remove session.
 *
 * @TableName "users"
 * @PrimaryKey "id"
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class User {

	const STATE_INACTIVE = "I"; // newly created account, not yet activated
	const STATE_ACTIVE = "A"; // fully operational account
	const STATE_EXPIRED = "E"; // account expired due to not logging in for long time
	const STATE_SUSPENDED = "S"; // account suspended by administrator
	const STATE_DELETED = "D"; // account deleted

	const SESSION_KEY = "userId";
	const ADMIN_LOGIN = "admin";
	const ADMIN_PASSWORD = "password";
	const TEST_PASSWORD = "test_password";

	public static $currentUser;

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
	 * @Transformation "CopyTransformation", "hashedPassword"
	 */
	public $password;

	public $hashedPassword;

	/**
	 * Password salt
	 * @var int
	 * @Column "salt"
	 * @NotNull
	 */
	public $salt;

	/**
	 * Password reset token
	 * @var int
	 * @Column "token"
	 */
	public $token;

	/**
	 * Password reset token expiry date
	 * @var int
	 * @Column "token_expires_on"
	 * @Transformation "DateTransformation"
	 */
	public $tokenExpiresOn;

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
	 * Administrator flag
	 * @var boolean
	 * @Column "is_admin"
	 * @NotNull
	 * @Transformation "BooleanTransformation"
	 */
	public $isAdmin;

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

	/**
	 * Constructor
	 *
	 * @param array $a database row as hash
	 */
	public function __construct(array $a = array()) {
		empty($a) or Persistence::revive($a, $this);
	}

	/**
	 * Return user by primary key or NULL
	 *
	 * @param int $id
	 * @return User|NULL
	 */
	public static function getById($id) {
		$sql = "SELECT * FROM users WHERE id = :id";
		$row = PDOHelper::fetchRow($sql, array("id" => $id));
		if (empty($row)) {
			return NULL;
		}
		return new User($row);
	}

	/**
	 * Return user by login name or NULL
	 *
	 * @param string $login
	 * @return User|NULL
	 */
	public static function getByLogin($login) {
		$sql = "SELECT * FROM users WHERE login = :login";
		$row = PDOHelper::fetchRow($sql, array("login" => $login));
		if (empty($row)) {
			return NULL;
		}
		return new User($row);
	}

	/**
	 * Return user by email address or NULL
	 *
	 * @param string $email
	 * @return User|NULL
	 */
	public static function getByEmail($email) {
		$sql = "SELECT * FROM users WHERE email = :email";
		$row = PDOHelper::fetchRow($sql, array("email" => $email));
		if (empty($row)) {
			return NULL;
		}
		return new User($row);
	}

	public static function getAllActive() {
		$filter = new UserFilter();
		$filter->active();
		return $this->filter($filter);
	}

	public static function getAll() {
		return $this->filter(new UserFilter());
	}

	/**
	 * Rerutn collection of users by filter criteria with id as key
	 *
	 * @param UserFilter $filter, default no filter (all users)
	 * @return User[]
	 */
	public static function filter(UserFilter $filter = NULL) {
		$filter or $filter = new UserFilter();
		$users = array();
		list ($where, $params) = $filter->get();
		$sql = "SELECT * FROM users WHERE $where";
		foreach (PDOHelper::fetchAll($sql, $params) as $row) {
			$users[$user->id] = new User($row);
		}
		return $users;
	}

	/**
	 * Save user into database and return its primary key
	 *
	 * @return int
	 */
	public function save() {
		if (NULL !== $this->password and $this->password !== $this->hashedPassword) {
			// hash password if updated
			$this->salt or $this->salt = rand(100, 999);
			$this->password = sha1($this->password . $this->salt);
			$this->hashedPassword = $this->password;
		} else {
			$this->password = NULL;
		}
		$id = Persistence::freeze($this);
		$this->id or $this->id = $id;
		return $id;
	}

	/**
	 * Return if user is an admin
	 *
	 * @return boolean
	 */
	public function isAdmin() {
		return (boolean)$this->isAdmin;
	}

	/**
	 * Validate string against hashed and salted password
	 *
	 * @param string $password
	 * @return boolean
	 */
	public function validatePassword($password) {
		return $this->password === self::makePassword($password . $this->salt);
	}

	/**
	 * Make password of given string and salt
	 *
	 * @param string $password
	 * @param int $salt optional
	 * @return string
	 */
	public static function makePassword($password, $salt = NULL) {
		return sha1($password . $salt);
	}

	/**
	 * Return if current user is logged in
	 *
	 * @return boolean
	 */
	public static function isLoggedIn() {
		return (boolean)HTTPHelper::session(self::SESSION_KEY);
	}

	/**
	 * Set current user as logged in
	 *
	 * @param User $currentUser
	 */
	public static function logIn(User $currentUser) {
		$_SESSION[self::SESSION_KEY] = $currentUser->id;
		self::$currentUser = $currentUser;
	}

	/**
	 * Set current user as logged out
	 */
	public static function logOut() {
		unset($_SESSION[self::SESSION_KEY]);
		self::$currentUser = NULL;
	}

	/**
	 * Return currently logged in user or NULL if not logged in
	 *
	 * @return User
	 */
	public static function getCurrent() {
		if (NULL === self::$currentUser) {
			$id = HTTPHelper::session("userId");
			$id and self::$currentUser = User::getById($id);
		}
		return self::$currentUser;
	}

	/**
	 * Return id of currently logged in user or NULL if not logged in
	 *
	 * @return int|NULL
	 */
	public static function getCurrentId() {
		return (NULL === self::$currentUser) ? NULL : self::$currentUser->id;
	}

	/**
	 * Return collection of trades
	 *
	 * @param TradeFilter $filter
	 * @return Trade[]
	 */
	public function getTrades(TradeFilter $filter = NULL) {
		$filter or $filter = new TradeFilter();
		$filter->setUserId($this->id);
		return Trade::filter($filter);
	}

	/**
	 * Return collection of offers
	 *
	 * @param OfferFilter $filter
	 * @return Offer[]
	 */
	public function getOffers(OfferFilter $filter = NULL) {
		$filter or $filter = new OfferFilter();
		$filter->setUserId($this->id);
		return Offer::filter($filter);
	}

}