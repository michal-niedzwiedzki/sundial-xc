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
		$this->page->alreadyInstalled = $connection and DB::checkMissingTables() === array();
	}

	/**
	 * @Public
	 * @Title "Sundial XC installation wizard - Step 2: Configuration setup"
	 */
	public function step4_admin() {
	}

}