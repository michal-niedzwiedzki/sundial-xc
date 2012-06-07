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
	 * Init locale settings and character encoding
	 */
	public static function initLocale() {
		setlocale(LC_ALL, "es_ES");
		mb_internal_encoding("UTF-8");
		mb_regex_encoding("UTF-8");
	}

	/**
	 * Init inclusion paths and autoloading
	 */
	public static function initPaths() {
		define("ROOT_DIR", realpath(dirname(__FILE__)));
		chdir(ROOT_DIR);
		set_include_path(ROOT_DIR . "/legacy" . PATH_SEPARATOR . ROOT_DIR . "/external/pear");
		spl_autoload_register("Bootstrap::autoloadHandler");
	}

	/**
	 * Init global defines and handlers
	 */
	public static function initEnvironment() {
		Assert::fileExists(ROOT_DIR . "/env.txt");
		define("ENV", trim(file_get_contents(ROOT_DIR . "/env.txt")));
		define("LIVE", Config::getInstance()->live);
		Debug::log("Init", Debug::DEBUG);
		register_shutdown_function("Bootstrap::shutdownHandler");
		set_error_handler("Bootstrap::errorHandler");
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
		$pdo = DB::getPDO();
		if ($pdo->inTransaction()) {
			$pdo->rollBack();
			Debug::log("Unfinished transaction - rollback initiated", Debug::WARNING);
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
Bootstrap::initLocale();
Bootstrap::initPaths();
Bootstrap::initEnvironment();

// legacy code
require_once ROOT_DIR . "/legacy/includes/inc.global.php";
require_once ROOT_DIR . "/legacy/includes/inc.config.php";

// shorthand for html escaping
function e($text) {
	return htmlspecialchars($text, ENT_QUOTES, "UTF-8");
}
