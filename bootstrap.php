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

// locale and encoding
setlocale(LC_ALL, "es_ES");
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

// paths
define("ROOT_DIR", realpath(dirname(__FILE__)));
set_include_path(ROOT_DIR . "/legacy" . PATH_SEPARATOR . ROOT_DIR . "/external/pear");

// class loader
function __autoload($className) {
	static $map;
	$map or $map = parse_ini_file(ROOT_DIR . "/classmap.ini", INI_SCANNER_RAW);
	if (isset($map[$className])) {
		include $map[$className];
	}
}

// check preconditions and make comfortable
Assert::fileExists(ROOT_DIR . "/env.txt");
define("ENV", trim(file_get_contents(ROOT_DIR . "/env.txt")));
define("LIVE", Config::getInstance()->live);
Debug::log("Init", Debug::DEBUG);

// shutdown handler
function applicationShutdown() {
	$pdo = DB::getPDO();
	if ($pdo->inTransaction()) {
		$pdo->rollBack();
		Debug::log("Unfinished transaction - rollback initiated", Debug::WARNING);
	}
}
register_shutdown_function("applicationShutdown");

// error handler
function applicationError($severity, $message, $file, $line, $context = array()) {
	Debug::log("PHP {$severity}: {$message}\nFile {$file}\nLine {$line}", Debug::ERROR);
}
set_error_handler("applicationError");

// legacy code
require_once ROOT_DIR . "/legacy/includes/inc.global.php";
require_once ROOT_DIR . "/legacy/includes/inc.config.php";

// maintenance
if (Config::getInstance()->site->maintenance) {
	header("HTTP/1.4 503 Service unavailable");
	echo "<html><head></head><body><h2>Service has been taken off-line for maintenance</h2><p>Apologies for inconveniences, please come back later.</p><pre>HTTP/1.1 503 Service unavailable</pre></body></html>";
	die();
}

// shorthand for html escaping
function e($text) {
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
