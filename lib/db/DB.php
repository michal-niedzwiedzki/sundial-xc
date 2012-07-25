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
		self::$silo = $silo;
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
		$silo = self::$silo
			? self::$silo
			: (class_exists("PHPUnit_Framework_TestCase") ? "testing" : "production");
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

	public static function initializeSystemAccount() {
		$systemMember = array(
			"member_id" => "ADMIN",
			"password" => sha1("password"),
			"member_role" => 9,
			"security_q" => NULL,
			"security_a" => NULL,
			"status" => "A",
			"member_note" => NULL,
			"admin_note" => "Special account created during install. Ok to inactivate once an Admin Level 2 acct has been created.",
			"join_date" => date("Y-m-d h:i:s"),
			"expire_date" => NULL,
			"away_date" => NULL,
			"account_type" => "S",
			"email_updates" => 7,
			"balance" => 0.00
		);
		PDOHelper::insert(DB::MEMBERS, $systemMember);
		$systemPerson = array(
			"person_id" => 1,
			"member_id" => "admin",
			"primary_member" => "Y",
			"directory_list" => "Y",
			"first_name" => "Special Admin",
			"last_name" => "Account",
			"mid_name" => NULL,
			"dob" => NULL,
			"mother_mn" => NULL,
			"email" => NULL,
			"address_city" => DEFAULT_CITY,
			"address_state_code" => DEFAULT_STATE,
			"address_post_code" => DEFAULT_ZIP_CODE,
			"address_country" => DEFAULT_COUNTRY
		);
		PDOHelper::insert(DB::PERSONS, $systemPerson);
	}

	public static function initializeCategories() {
		$categories = array(
			"Ayuda desplazamientos/transporte",
			"Atención de mayores",
			"Atención personas necesitadas",
			"Cocina",
			"Costura",
			"Trabajos domésticos",
			"Cuidado de animales",
			"Jardinería",
			"Decoración",
			"Informática para uso domésticos",
			"Reparaciones",
			"Educación",
			"Bienestar",
			"Compañia",
			"Familia",
			"Atención de niños",
			"Tramites y gestiones",
			"Negocios",
			"Belleza",
			"Recreo, Ocio, Deporte",
		);
		foreach ($categories as $category) {
			PDOHelper::insert(DB::CATEGORIES, array("parent_id" => NULL, "description" => $category));
		}
	}

	public static function initializeSettings() {
		$columns = array("id", "name", "display_name", "typ", "current_value", "options", "default_value", "max_length", "descrip", "section");
		$values = array(
			array('8', 'LEECH_EMAIL_URUNLOCKED', '\'Account Restriction Lifted\' Email', 'longtext', '', '', 'Restrictions on your account have been lifted.', '', 'Define email that is sent out when restrictions are lifted on an account.', '3'),
			array('6', 'LEECH_EMAIL_URLOCKED', '\'Account Restricted\' Email', 'longtext', '', '', 'Dear Member\r\n\r\nWe have been reviewing members balances as we are concerned to ensure that trading goes back and forth on an equitable basis so that members are able to keep their accounts close to zero.  We recognise that situations sometimes occur that lead to things getting out of balance.  Therefore to assist you, we have restricted expenditure on your account for the time being. If have any queries about this, or if we can assist you in any particular way, please let us know, and we will review the situation in due course. The LETS Administrator ', '', 'Define email that is sent out when restrictions are imposed on an account.', '3'),
			array('10', 'MEM_LIST_DISPLAY_BALANCE', 'Display Member Balance', 'bool', '', '', 'TRUE', '', 'Do you want to display member balances in the Members List? (Balances are always visible to Admins and Committee members regardless of what is set here.)', '7'),
			array('11', 'TAKE_SERVICE_FEE', 'Enable Take Service Charge', 'bool', '', '', 'TRUE', '', 'Do you want the option of taking a service charge from members as and when?', '2'),
			array('12', 'SHOW_INACTIVE_MEMBERS', 'Show Inactive Members in Members List', 'bool', '', '', 'FALSE', '', 'Do you want to display Inactive members in the Member List?', '7'),
			array('13', 'SHOW_RATE_ON_LISTINGS', 'Show Rate on Listings', 'bool', '', '', 'TRUE', '', 'Do you want to display the Rate alongside the offers/wants in the main listings?', '7'),
			array('14', 'SHOW_POSTCODE_ON_LISTINGS', 'Show Postcode on Listings', 'bool', '', '', 'TRUE', '', 'Do you want to display the PostCode alongside the offers/wants in the main listings?', '7'),
			array('15', 'NUM_CHARS_POSTCODE_SHOW_ON_LISTINGS', 'Postcode Length (in chars)', 'int', '', '', '4', '', 'If you have elected to display the postcode on offers/wants listings, how much of the PostCode do you want to show? (the number you enter will be the number of characters displayed, so for eg if you just want to show the first 3 characters of the postcode then put 3.', '7'),
			array('16', 'OVRIDE_BALANCES', 'Enable Balance Override', 'bool', '', '', 'FALSE', '', 'Do you want admins to have the option to override Balances on a per member basis? This can be useful during the initial site set-up for inputting existing balances. Link will appear in admin panel if set to TRUE.  Use with CAUTION to avoid the database going out of balance', '6'),
			array('17', 'MEMBERS_CAN_INVOICE', 'Enable Member-to-Member Invoicing', 'bool', '', '', 'TRUE', '', 'Do you want to allow members to invoice one-another via the site? (The recipient is always given the option to confirm/reject payment of the invoice)', '2'),
			array('18', 'ALLOW_IMAGES', 'Allow Members to Upload Images', 'bool', '', '', 'TRUE', '', 'Do you want to allow members to upload an image of themselves, to be displayed with their personal profile?', '4'),
			array('19', 'SOC_NETWORK_FIELDS', 'Enable Social Networking Fields', 'bool', '', '', 'TRUE', '', 'Do you want to enable the Social Networking profile fields (Age, Sex, etc)?', '4'),
			array('20', 'OOB_ACTION', 'Out Of Balance Behaviour', 'multiple', '', 'FATAL,SILENT', 'SILENT', '', ' If, whilst processing a trade, the database is found to be out of balance, what should the system do?\n\nFATAL = Aborts the trade and informs the user why.\n\nSILENT = Continues with trade, displays no notifications whatsoever (NOTE: you can still set the option below to have an email notification sent to the admin)', '6'),
			array('21', 'OOB_EMAIL_ADMIN', 'Email Admin on Out Of Balance', 'bool', '', '', 'TRUE', '', 'Should the system send the Admin an email when the database is found to be out of balance?', '6'),
			array('24', 'EMAIL_FROM', 'Email From Address', '', '', '', 'From: reply@my-domain.org', '', 'Email sent from this site will show as coming from this address', '1'),
			array('25', 'USE_RATES', 'Use Rates Fields', 'bool', '', '', 'TRUE', '', 'If turned on, listings will include a \"Rate\" field', '7'),
			array('26', 'TAKE_MONTHLY_FEE', 'Enable Monthly Fee', 'bool', '', '', 'TRUE', '', 'Do you want to enable Monthly Fees', '2'),
			array('27', 'MONTHLY_FEE', 'Monthly Fee Amount', 'int', '', '', '1', '', 'How much should the Monthly Fee be?', '2'),
			array('28', 'EMAIL_LISTING_UPDATES', 'Send Listing Updates via Email', 'bool', '', '', 'FALSE', '', 'Should users receive automatic updates for new and modified listings?', '1'),
			array('29', 'DEFAULT_UPDATE_INTERVAL', 'Default Email Listings Update Interval', 'multiple', '', 'NEVER,WEEKLY,MONTHLY', 'NEVER', '', 'If automatic updates are sent, this is the default interval.', '1'),
			array('34', 'ALLOW_INCOME_SHARES', 'Allow Income Sharing', 'bool', '', null, 'TRUE', '99999', 'Do you want to allow members to share a percentage of any income they generate with another account of their choosing? The member can specify the exact percentage they wish to donate.', '2'),
			array('35', 'LEECH_NOTICE', 'Message Displayed to Leecher who tries to trade', 'longtext', '', '', 'Restrictions have been imposed on your account which prevent you from trading outwards, Please contact the administrator for more information.', '', 'Leecher sees this notice when trying to send money.', '3'),
			array('36', 'SHOW_GLOBAL_FEES', 'Show monthly fees and service charges in global exchange view', 'bool', '', null, 'FALSE', '', 'Do you want to show monthly fees and service charges in the global exchange view? (Note: individual members will still be able to see this in their own personal exchange history).', '7'),
		);
		$settings = array_combine($columns, $values);
		foreach ($settings as $s) {
			PDOHelper::insert(DB::SETTINGS, $s);
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
		$currentVersionFile = ROOT_DIR . "/var/migrations/version.txt";
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