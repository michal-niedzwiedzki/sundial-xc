<?php

/**
 * Database access service class
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class DB {

	const DEFAULT_CONNECTION = 'DEFAULT';

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

	private static $tables = array(
		DB::MEMBERS => "
			CREATE TABLE member (
				member_id VARCHAR(15) NOT NULL DEFAULT '',
				password VARCHAR(50) NOT NULL DEFAULT '',
				member_role CHAR(1) NOT NULL DEFAULT '',
				security_q VARCHAR(25) DEFAULT NULL,
				security_a VARCHAR(15) DEFAULT NULL,
				status CHAR(1) NOT NULL DEFAULT '',
				member_note VARCHAR(100) DEFAULT NULL,
				admin_note TEXT DEFAULT '',
				join_date DATE NOT NULL DEFAULT '0000-00-00',
				expire_date DATE DEFAULT NULL,
				away_date DATE DEFAULT NULL,
				account_type CHAR(1) NOT NULL DEFAULT '',
				email_updates INT(3) UNSIGNED NOT NULL DEFAULT 0,
				balance DECIMAL(8,2) NOT NULL DEFAULT 0.00,
				confirm_payments INT(1) DEFAULT 0,
				restriction INT(1),
				PRIMARY KEY (member_id)
			)
			ENGINE InnoDB
			DEFAULT CHARACTER SET utf8
		",
		DB::PERSONS => "
			CREATE TABLE person (
				person_id MEDIUMINT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
				member_id VARCHAR(15) NOT NULL DEFAULT '',
				primary_member CHAR(1) NOT NULL DEFAULT '',
				directory_list CHAR(1) NOT NULL DEFAULT '',
				first_name VARCHAR(20) NOT NULL DEFAULT '',
				last_name VARCHAR(30) NOT NULL DEFAULT '',
				mid_name VARCHAR(20) DEFAULT NULL,
				dob DATE DEFAULT NULL,
				mother_mn VARCHAR(30) DEFAULT NULL,
				email VARCHAR(40) DEFAULT NULL,
				phone1_area CHAR(5) DEFAULT NULL,
				phone1_number VARCHAR(30) DEFAULT NULL,
				phone1_ext VARCHAR(4) DEFAULT NULL,
				phone2_area CHAR(5) DEFAULT NULL,
				phone2_number VARCHAR(30) DEFAULT NULL,
				phone2_ext VARCHAR(4) DEFAULT NULL,
				fax_area CHAR(3) DEFAULT NULL,
				fax_number VARCHAR(30) DEFAULT NULL,
				fax_ext VARCHAR(4) DEFAULT NULL,
				address_street1 VARCHAR(50) DEFAULT NULL,
				address_street2 VARCHAR(50) DEFAULT NULL,
				address_city VARCHAR(50) NOT NULL DEFAULT '',
				address_state_code CHAR(50) NOT NULL DEFAULT '',
				address_post_code VARCHAR(20) NOT NULL DEFAULT '',
				address_country VARCHAR(50) NOT NULL DEFAULT '',
				about_me TEXT NOT NULL DEFAULT '',
				age VARCHAR(20) DEFAULT NULL,
				sex VARCHAR(1) DEFAULT NULL,
				PRIMARY KEY (person_id)
			)
			ENGINE MyISAM
			DEFAULT CHARACTER SET utf8
		",
		DB::LISTINGS => "
			CREATE TABLE listings (
				title VARCHAR(60) NOT NULL DEFAULT '',
				description TEXT,
				category_code SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
				member_id VARCHAR(15) NOT NULL DEFAULT '',
				rate VARCHAR(30) DEFAULT NULL,
				status CHAR(1) NOT NULL DEFAULT '',
				posting_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				expire_date DATE DEFAULT NULL,
				reactivate_date DATE DEFAULT NULL,
				type CHAR(1) NOT NULL DEFAULT '',
				PRIMARY KEY (title, category_code, member_id,type)
			)
			ENGINE MyISAM
			DEFAULT CHARACTER SET utf8
		",
		DB::CATEGORIES => "
			CREATE TABLE categories (
				category_id SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
				parent_id SMALLINT(4) UNSIGNED DEFAULT NULL,
				description VARCHAR(40) NOT NULL DEFAULT '',
				PRIMARY KEY (category_id)
			)
			ENGINE MyISAM
			DEFAULT CHARACTER SET utf8
		",
		DB::TRADES => "
			CREATE TABLE trades (
				trade_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
				trade_date TIMESTAMP NOT NULL,
				status CHAR(1) DEFAULT NULL,
				member_id_from VARCHAR(15) NOT NULL DEFAULT '',
				member_id_to VARCHAR(15) NOT NULL DEFAULT '',
				amount DECIMAL(8,2) NOT NULL DEFAULT 0.00,
				category SMALLINT(4) UNSIGNED NOT NULL DEFAULT 0,
				description VARCHAR(255) DEFAULT NULL,
				type CHAR(1) NOT NULL DEFAULT '',
				PRIMARY KEY (trade_id)
			)
			ENGINE InnoDB
			DEFAULT CHARACTER SET utf8
		",
		DB::LOGGING => "
			CREATE TABLE admin_activity (
				log_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
				log_date TIMESTAMP NOT NULL,
				admin_id VARCHAR(15) NOT NULL DEFAULT '',
				category CHAR(1) NOT NULL DEFAULT '',
				action CHAR(1) NOT NULL DEFAULT '',
				ref_id VARCHAR(15) NOT NULL DEFAULT '',
				note VARCHAR(100) DEFAULT NULL,
				PRIMARY KEY (log_id)
			)
			ENGINE InnoDB
			DEFAULT CHARACTER SET utf8
		",
		DB::LOGINS => "
			CREATE TABLE logins (
				member_id VARCHAR(15) NOT NULL DEFAULT '',
				total_failed MEDIUMINT(6) UNSIGNED NOT NULL DEFAULT 0,
				consecutive_failures MEDIUMINT(3) UNSIGNED NOT NULL DEFAULT 0,
				last_failed_date TIMESTAMP NOT NULL,
				last_success_date TIMESTAMP NOT NULL DEFAULT '00000000000000',
				PRIMARY KEY (member_id)
			)
			ENGINE InnoDB
			DEFAULT CHARACTER SET utf8
		",
		DB::FEEDBACK => "
			CREATE TABLE feedback (
				feedback_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
				feedback_date TIMESTAMP NOT NULL,
				status CHAR(1) NOT NULL DEFAULT '',
				member_id_author VARCHAR(15) NOT NULL DEFAULT '',
				member_id_about VARCHAR(15) NOT NULL DEFAULT '',
				trade_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0,
				rating CHAR(1) NOT NULL DEFAULT '',
				comment TEXT,
				PRIMARY KEY (feedback_id)
			)
			ENGINE InnoDB
			DEFAULT CHARACTER SET utf8
		",
		DB::REBUTTAL => "
			CREATE TABLE feedback_rebuttal (
				rebuttal_id MEDIUMINT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
				rebuttal_date TIMESTAMP NOT NULL,
				feedback_id MEDIUMINT(8) UNSIGNED DEFAULT NULL,
				member_id VARCHAR(15) NOT NULL DEFAULT '',
				comment VARCHAR(255) DEFAULT NULL,
				PRIMARY KEY (rebuttal_id)
			)
			ENGINE InnoDB
			DEFAULT CHARACTER SET utf8
		",
		DB::NEWS => "
			CREATE TABLE news (
				news_id MEDIUMINT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
				title VARCHAR(100) NOT NULL DEFAULT '',
				description TEXT NOT NULL,
				sequence DECIMAL(6,4) NOT NULL DEFAULT 0.0000,
				expire_date DATE DEFAULT NULL,
				PRIMARY KEY (news_id)
			)
			ENGINE InnoDB
			DEFAULT CHARACTER SET utf8
		",
		DB::UPLOADS => "
			CREATE TABLE uploads (
				upload_id MEDIUMINT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
				upload_date TIMESTAMP NOT NULL,
				title VARCHAR(100) NOT NULL DEFAULT '',
				type CHAR(1) NOT NULL DEFAULT '',
				filename VARCHAR(100) DEFAULT NULL,
				note VARCHAR(100) DEFAULT NULL,
				PRIMARY KEY (upload_id)
			)
			ENGINE InnoDB
			DEFAULT CHARACTER SET utf8
		",
		DB::SESSION => "
			CREATE TABLE session (
				id CHAR(32) NOT NULL,
				data TEXT,
				ts TIMESTAMP,
				PRIMARY KEY(id),
				KEY(ts)
			)
			ENGINE MyISAM
			DEFAULT CHARACTER SET utf8
		",
		DB::SETTINGS => "
			CREATE TABLE settings (
				id int(11) NOT NULL auto_increment,
				name varchar(255) default NULL,
				display_name varchar(255) default NULL,
				typ varchar(10) default NULL,
				current_value text,
				options varchar(255) default NULL,
				default_value text,
				max_length varchar(5) default '99999',
				descrip text,
				section int(1) default NULL,
				PRIMARY KEY  (id)
			)
			ENGINE MyISAM
			AUTO_INCREMENT 35
			DEFAULT CHARACTER SET utf8
		",
		DB::INCOME_TIES => "
			CREATE TABLE income_ties (
 				id INT(11) NOT NULL AUTO_INCREMENT,
				member_id VARCHAR(15) DEFAULT NULL,
				tie_id VARCHAR(15) DEFAULT NULL,
				percent INT(3) DEFAULT NULL,
				PRIMARY KEY  (id)
			)
			ENGINE MyISAM
			AUTO_INCREMENT 12
			DEFAULT CHARACTER SET utf8
		",
		DB::PAGES => "
			CREATE TABLE cdm_pages (
				id int(11) NOT NULL AUTO_INCREMENT,
				date INT(30) DEFAULT NULL,
				title VARCHAR(255) DEFAULT NULL,
				body TEXT,
				active INT(1) DEFAULT 1,
				permission INT(2),
				PRIMARY KEY (id)
			)
			ENGINE MyISAM
			AUTO_INCREMENT 6
			DEFAULT CHARACTER SET utf8
		",
		DB::TRADES_PENDING => "
			CREATE TABLE trades_pending (
				id mediumint(8) unsigned NOT NULL auto_increment,
				trade_date timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
				member_id_from varchar(15) NOT NULL default '',
				member_id_to varchar(15) NOT NULL default '',
				amount decimal(8,2) NOT NULL default 0.00,
				category smallint(4) unsigned NOT NULL default 0,
				description varchar(255) default NULL,
				typ varchar(1) default NULL,
				status varchar(1) default 'O',
				member_to_decision varchar(2) default '1',
				member_from_decision varchar(2) default '1',
				PRIMARY KEY (id)
			)
			ENGINE MyISAM
			AUTO_INCREMENT 17
			DEFAULT CHARACTER SET utf8
		",
	);

	/**
	 * PDO instance
	 * @var PDO
	 */
	private static $pdo;

	private function __construct() { }

	/**
	 * Indicate if PDO instance is created
	 *
	 * @return boolean
	 */
	public static function hasPDO() {
		return self::$pdo instanceof PDO;
	}

	/**
	 * Return PDO instance
	 *
	 * @return PDO
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function getPDO() {
		if (!isset(self::$pdo)) {
			$config = Config::getInstance();
			Assert::isObject($config->db);
			Assert::hasProperty("database", $config->db);
			Assert::hasProperty("server", $config->db);
			Assert::hasProperty("username", $config->db);
			Assert::hasProperty("password", $config->db);
			$dsn = "mysql:dbname={$config->db->database};host={$config->db->server}";
			$options = array(
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			);
			self::$pdo = new PDO($dsn, $config->db->username, $config->db->password, $options);
		}
		return self::$pdo;
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
		$pdo = DB::getPDO();
		foreach (DB::$tables as $table => $create) {
			try {
				Assert::true($pdo->query($create));
			} catch (Exception $e) {
				$pdo->query("DROP TABLE IF EXISTS $table");
				Assert::true($pdo->query($create));
			}
		}
	}

	/**
	 * Return missing tables
	 *
	 * @return string[]
	 */
	public static function checkMissingTables() {
		$missing = array();
		$pdo = DB::getPDO();
		foreach (DB::$tables as $table => $create) {
			try {
				$pdo->query("DESC $table");
			} catch (PDOException $e) {
				$missing[] = $table;
			}
		}
		return $missing;
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
	 * Drop all tables used by system
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function drop() {
		$pdo = DB::getPDO();
		foreach (DB::$tables as $table => $create) {
			Assert::true($db->query("DROP TABLE IF EXISTS $database CASCADE"));
		}
	}

}