<?php

final class InstallController extends Controller {

	public function __construct() {
		PageView::getInstance()->showSidebar = FALSE;
	}

	/**
	 * @Public
	 * @Title "Sundial XC installation wizard"
	 */
	public function index() { }

	/**
	 * @Public
	 * @Title "Sundial XC installation wizard - Step 1: PHP setup"
	 */
	public function step1_php() {
		$this->page->gettext = function_exists("gettext");
		$this->page->gd = function_exists("gd_info");
		$this->page->pdo = class_exists("PDO");
		$this->page->log = is_writable(ROOT_DIR . "/log");
		$this->page->migrations = is_writable(ROOT . "/var/migrations/version.txt");
	}

	/**
	 * @Public
	 * @Title "Sundial XC installation wizard - Step 2: Configuration setup"
	 */
	public function step2_config() {
		$this->page->env = ENV;
		$this->page->defaultConfig = ENV === "example";
	}

	/**
	 * @Public
	 * @Title "Sundial XC installation wizard - Step 3: Database setup"
	 */
	public function step3_database() {
		// try to connect
		try {
			DB::getPDO();
			$connection = TRUE;
		} catch (PDOException $e) {
			$connection = FALSE;
		}
		$this->page->connection = $connection;

		// check if default connection settings
		$config = Config::getInstance();
		$this->page->default = $config->db->database === "sundialxc" and $config->db->server === "localhost"
			and $config->db->username === "sundialxc" and $config->db->password === "sundialxc";

		// check if already installed
		$alreadyInstalled = $connection and count(DB::getTables()) > 0;
		$this->page->alreadyInstalled = $alreadyInstalled;

		// install
		$this->page->ok = FALSE;
		if ($connection and !$alreadyInstalled) {
			$this->page->of = DB::create();
		}
	}

	/**
	 * @Public
	 * @Title "Sundial XC installation wizard - Step 4: Administrator password"
	 */
	public function step4_admin() {
		$admin = new cMember();
		$admin->LoadMember("admin");
		$defaultPassword = $admin->password === sha1(cMember::DEFAULT_PASSWORD);
		$this->page->defaultPassword = $defaultPassword;
		$this->page->passwordChanged = FALSE;
		if ($defaultPassword and $password = HTTPHelper::post("password")) {
			PDOHelper::update(DB::MEMBERS, array("password" => sha1($password)), "id = :id", array("id" => "admin"));
			$this->passwordChanged = TRUE;
		}
	}

	/**
	 * @Public
	 * @Title "Sundial XC installation wizard - Installation complete"
	 */
	public function step5_done() { }

}