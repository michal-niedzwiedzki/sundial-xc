<?php

/**
 * Sundial XC
 *
 * Copyright (C) 2012  Michał Rudnicki <michal.rudnicki@epsi.pl>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @see agpl-3.0.txt
 * @see README.md
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */

/**
 * Bootstrap
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class Bootstrap {

	/**
	 * Init inclusion paths and autoloading
	 */
	public static function initPaths() {
		define("ROOT_DIR", realpath(dirname(__FILE__)));
		chdir(ROOT_DIR);
		set_include_path(ROOT_DIR . "/legacy" . PATH_SEPARATOR . ROOT_DIR . "/external/pear" . PATH_SEPARATOR . get_include_path());
		spl_autoload_register("Bootstrap::autoloadHandler");
	}

	/**
	 * Init locale settings and character encoding
	 */
	public static function initLocale() {
		mb_internal_encoding("UTF-8");
		mb_regex_encoding("UTF-8");

		if (isset($_COOKIE["locale"]) and $_COOKIE["locale"]) {
			$locale = $_COOKIE["locale"];
		} elseif (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			$locale = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
		} else {
			$locale = "en_US";
		}
		preg_match("/[A-Za-z_]+/", $locale) or $locale = "en_US";
		file_exists(ROOT_DIR . "/locale/{$locale}/MESSAGES") or $locale = "en_US";

		putenv("LC_ALL={$locale}");
		setlocale(LC_ALL, $locale);
		bindtextdomain("messages", ROOT_DIR . "/locale");
		textdomain("messages");
	}

	/**
	 * Init global defines and handlers
	 */
	public static function initEnvironment() {
		$readable = is_readable(ROOT_DIR . "/env.txt");
		define("ENV", $readable ? trim(file_get_contents(ROOT_DIR . "/env.txt")) : "example");
		define("LIVE", Config::getInstance()->site->live);
		Debug::log("Bootstrap", Debug::DEBUG);
		$readable or Debug::log("Cannot read file 'env.txt', assuming 'example' as environment", Debug::WARNING);
		register_shutdown_function("Bootstrap::shutdownHandler");
		set_error_handler("Bootstrap::errorHandler");
	}

	/**
	 * Init user session
	 */
	public static function initSession() {
		session_id("SundialXC");
		session_start();
		if (isset($_SERVER["REQUEST_METHOD"]) and $_SERVER["REQUEST_METHOD"] === "POST") {
			define("CSRF", isset($_SESSION["csrf"]) ? $_SESSION["csrf"] : NULL);
		} else {
			define("CSRF", rand());
			$_SESSION["csrf"] = CSRF;
		}
		Debug::log("CSRF token is " . CSRF, Debug::DEBUG);
	}

	/**
	 * Class loading handler
	 */
	public static function autoloadHandler($className) {
		static $map;
		$map or $map = parse_ini_file(ROOT_DIR . "/classmap.ini", INI_SCANNER_RAW);
		if (isset($map[$className])) {
			include $map[$className];
		}
	}

	/**
	 * Shutdown handler
	 */
	public static function shutdownHandler() {
		if (DB::hasPDO()) {
			$pdo = DB::getPDO();
			if ($pdo->inTransaction()) {
				Debug::log("Unfinished transaction - rollback initiated", Debug::WARNING);
				$pdo->rollBack();
				Debug::log("Unfinished transaction - rollback complete", Debug::WARNING);
			}
		}
	}

	/**
	 * Error handler
	 */
	public static function errorHandler($severity, $message, $file, $line, $context = array()) {
		Debug::log("PHP {$severity}: {$message}\nFile {$file}\nLine {$line}", Debug::ERROR);
	}

}

// bootstrap
Bootstrap::initPaths();
Bootstrap::initLocale();
Bootstrap::initEnvironment();
Bootstrap::initSession();

// legacy code
require_once ROOT_DIR . "/legacy/includes/inc.global.php";
require_once ROOT_DIR . "/legacy/includes/inc.config.php";

// shorthand for html escaping
function e($text) {
	return htmlspecialchars($text, ENT_QUOTES, "UTF-8");
}
