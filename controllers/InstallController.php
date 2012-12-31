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
		$this->view->gettext = function_exists("gettext");
		$this->view->gd = function_exists("gd_info");
		$this->view->pdo = class_exists("PDO");
		$this->view->log = is_writable(ROOT_DIR . "/log");
		$this->view->migrations = is_writable(ROOT . "/var/migrations/version.txt");
	}

	/**
	 * @Public
	 * @Title "Sundial XC installation wizard - Step 2: Configuration setup"
	 */
	public function step2_config() {
		$this->view->env = ENV;
		$this->view->defaultConfig = ENV === "example";
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
		$this->view->connection = $connection;

		// check if default connection settings
		$config = Config::getInstance();
		$this->view->default = $config->db->database === "sundialxc" and $config->db->server === "localhost"
			and $config->db->username === "sundialxc" and $config->db->password === "sundialxc";

		// check if already installed
		$alreadyInstalled = $connection and count(DB::getTables()) > 0;
		$this->view->alreadyInstalled = $alreadyInstalled;

		// install
		$this->view->ok = FALSE;
		if ($connection and !$alreadyInstalled) {
			$this->view->of = DB::create();
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
		$this->view->defaultPassword = $defaultPassword;
		$this->view->passwordChanged = FALSE;
		if ($defaultPassword and $password = HTTPHelper::post("password")) {
			PDOHelper::update(
				"member",
				array("password" => sha1($password)), "id = :id", array("id" => "admin")
			);
			$this->passwordChanged = TRUE;
		}
	}

	/**
	 * @Public
	 * @Title "Sundial XC installation wizard - Installation complete"
	 */
	public function step5_done() { }

}