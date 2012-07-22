<?php

require_once dirname(__FILE__) . "/../../bootstrap.php";

ini_set('display_errors', "On");
error_reporting(E_ALL | E_STRICT);

// check if called from command line
if (LIVE and !isset($argc)) {
	header("HTTP/1.1 403 Forbidden", TRUE, 403);
	echo "<html><head></head><body><h2>Forbidden</h2><p>Cron service can only be invoked from command line</p><pre>HTTP/1.1 403 Forbidden";
	die();
}

// check maintenance flag
if (Config::getInstance()->site->maintenance) {
	die();
}

// run cron
Cron::getInstance()->runNextJob();
