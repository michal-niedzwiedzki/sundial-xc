<?php

require_once dirname(__FILE__) . "/../../bootstrap.php";

ini_set('display_errors', "On");
error_reporting(E_ALL | E_STRICT);

$d = Dispatcher::getInstance();
$d->addFilter(new ContentTypeDispatchFilter());
$d->addFilter(new RestRouteDispatchFilter());

Debug::log("Prefiltering", Debug::DEBUG);
$d->preFilter();
Debug::log("Dispatch and execution", Debug::DEBUG);
try {
	$d->dispatch();
} catch (Exception $e) {
	if (!Config::getInstance()->live) {
		Debug::log($e->getMessage() . PHP_EOL . $e->getTraceAsString(), Debug::ERROR);
	}
	PageView::getInstance()->setMessage("Action could not be completed due to software problem.");
}
Debug::log("Postfiltering", Debug::DEBUG);
$d->postFilter();
