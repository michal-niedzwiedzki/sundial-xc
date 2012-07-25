<?php

/**
 * Database access service class
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class DB {

	const DEFAULT_CONNECTION = "DEFAULT";

	const SILO_PRODUCTION = "production";
	const SILO_TESTING = "testing";

	const MEMBERS = "member";
	const USERS = "member";
	const PERSONS = "person";
	const LISTINGS = "listings";
	const CATEGORIES = "categories";
	const TRADES = "trades";
	const LOGGING = "admin_activity";
	const LOGINS = "logins";
	const FEEDBACK = "feedback";
	const REBUTTAL = "feedback_rebuttal";
	const NEWS = "news";
	const UPLOADS = "uploads";
	const SESSION = "session";
	const SETTINGS = "settings";
	const INCOME_TIES = "income_ties";
	const PAGES = "cdm_pages";
	const TRADES_PENDING = "trades_pending";

	/**
	 * PDO instance
	 * @var PDO
	 */
	private static $pdo;

	/**
	 * Database silo
	 * @var string
	 */
	private static $silo;

	/**
	 * Indicate if PDO instance is created
	 *
	 * @return boolean
	 */
	public static function hasPDO() {
		return self::$pdo instanceof PDO;
	}

	/**
	 * Mandate use of specific database silo
	 *
	 * @param string $silo can be "production" or "testing"
	 */
	public static function useSilo($silo) {
		if (self::$silo and self::$silo !== $silo) {
			throw new Exception("Silo already set to {self::$silo}");
		}
		if ($silo !== self::SILO_PRODUCTION and $silo !== self::SILO_TESTING) {
			throw new Exception("Silo can only be 'production' or 'testing'");
		}
		self::$silo = $silo;
	}

	/**
	 * Return database silo in use
	 *
	 * @return string
	 */
	public static function getSilo() {
		if (!self::$silo) {
			self::$silo = (class_exists("PHPUnit_Framework_TestCase") ? "testing" : "production");
		}
		return self::$silo;
	}

	/**
	 * Return PDO instance
	 *
	 * @return PDO
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function getPDO() {
		if (isset(self::$pdo)) {
			return self::$pdo;
		}

		// check silo
		$silo = self::getSilo();
		$config = Config::getInstance();
		Assert::isObject($config->database);
		Assert::isObject($config->database->$silo);

		// check connection details
		$connection = $config->database->$silo;
#		Assert::hasProperty("database", $connection);
#		Assert::hasProperty("server", $connection);
#		Assert::hasProperty("username", $connection);
#		Assert::hasProperty("password", $connection);

		// connect to database
		$dsn = "mysql:dbname={$connection->database};host={$connection->server}";
		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		);
		return self::$pdo = new PDO($dsn, $connection->username, $connection->password, $options);
	}

	/**
	 * Return if InnoDB engine is present
	 *
	 * @return boolean
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function hasInnoDB() {
		return "YES" === PDOHelper::fetchCell("Value", "SHOW VARIABLES LIKE 'have_innodb'");
	}

	/**
	 * Create all tables required by system
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function create() {
		$silo = self::$silo ? self::$silo : DB::SILO_PRODUCTION;
		foreach (DB::getMigrations() as $version) {
			DB::migrate(TRUE, $silo, $version);
		}
	}

	/**
	 * Return all tables in database
	 *
	 * @return string[]
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function getTables() {
		$tables = array();
		$pdo = DB::getPDO();
		$stmt = $pdo->prepare("SHOW TABLES");
		$stmt->execute();
		foreach ($stmt->fetchAll() as $row) {
			$tables[] = reset($row);
		}
		return $tables;
	}

	/**
	 * Drop all tables used by system
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function drop() {
		$pdo = DB::getPDO();
		foreach (DB::getTables() as $table) {
			Assert::true($pdo->query("DROP TABLE IF EXISTS $table CASCADE"));
		}
	}

	/**
	 * Truncate all tables used by system
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function truncate() {
		$pdo = DB::getPDO();
		foreach (DB::getTables() as $table) {
			Assert::true($pdo->query("TRUNCATE TABLE $table"));
		}
	}

	public static function getMigrations() {
		$versions = array();
		foreach (new DirectoryIterator(ROOT_DIR . "/migrations") as $entry) {
			if ($entry->isDir() and strpos($entry->getFilename(), ".") > 0) {
				$versions[] = $entry->getFilename();
			}
		}
		// FIXME ensure correct order
		return $versions;
	}

	/**
	 * Run database migration transactionally
	 *
	 * @param boolean $upgrade TRUE for upgrade, FALSE for downgrade
	 * @param string $silo eiter "production" or "testing"
	 * @param string $version migration identifier
	 * @param resource $output where to write output messages, default NULL
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function migrate($upgrade, $silo, $version, $output = NULL) {
		// check if version file writable
		$currentVersionFile = ROOT_DIR . "/var/migrations/version.{$silo}.txt";
		if (!is_writable($currentVersionFile)) {
			throw new Exception("File $currentVersionFile must we writable");
		}

		// get migration directory
		$dir = $upgrade
			? ROOT_DIR . "/migrations/$version/upgrade"
			: ROOT_DIR . "/migrations/$version/downgrade";

		// check if base version matches source version
		$baseVersionFile = ROOT_DIR . "/migrations/$version/base.txt";
		if (!is_readable($baseVersionFile)) {
			throw new Exception("File $baseVersionFile must be readable");
		}
		$baseVersion = trim(file_get_contents($baseVersionFile)); // version to upgrade from, or downgrade to declared in migration
		$sourceVersion = $upgrade ? $baseVersion : $version; // version before migration
		$targetVersion = $upgrade ? $version : $baseVersion; // version after migration
		$currentVersion = trim(file_get_contents($currentVersionFile)); // current version
		if ($currentVersion != $sourceVersion) {
			throw new Exception("Cannot migrate to '$targetVersion', as migration can only be started in '$sourceVersion', whereas current version is '$currentVersion'");
		}

		// fetch migration files
		$files = array();
		foreach (new DirectoryIterator($dir) as $finfo) {
			$finfo->isDir() or $files[] = $finfo->getPathname();
		}
		$upgrade ? sort($files) : rsort($files);

		// run migrations
		DB::useSilo($silo);
		$pdo = DB::getPDO();
		$pdo->beginTransaction();
		try {
			foreach ($files as $file) {
				$output and fwrite($output, "... $file\n");
				$pdo->query(file_get_contents($file));
			}
		} catch (Exception $e) {
			$output and fwrite($output, "Rolling back\n");
			$pdo->rollBack();
			throw $e;
		}
		$output and fwrite($output, "Committing transaction\n");
		$pdo->commit();

		// update base version
		$output and fwrite($output, "Updating current version to $targetVersion\n");
		file_put_contents($currentVersionFile, $targetVersion);
	}

}