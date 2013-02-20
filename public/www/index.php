<?php

require_once dirname(__FILE__) . "/../../bootstrap.php";

header("Content-Type: text/html; charset=UTF-8");

// check maintenance flag
if (Config::getInstance()->site->maintenance) {
	header("HTTP/1.1 503 Service unavailable", TRUE, 503);
	echo "<html><head></head><body><h2>Service has been taken off-line for maintenance</h2><p>Apologies for inconveniences, please come back later.</p><pre>HTTP/1.1 503 Service unavailable</pre></body></html>";
	die();
}

// set up dispatcher and filtering
$d = Dispatcher::getInstance();
$d->addFilter(new HTTPResponseCodeDispatchFilter()); // picks up HTTP response code from controller action annotation
LIVE or $d->addFilter(new DebugViewDispatchFilter()); // outputs logged events to the screen
LIVE or $d->addFilter(new RequestDebugDispatchFilter()); // debugs POST requests
$d->addFilter(new CSRFDispatchFilter()); // checks CSRF token for POST requests
$d->addFilter(new PathInfoDispatchFilter()); // picks up route info from $_SERVER['PATH_INFO']
$d->addFilter(new PageNotFoundDispatchFilter()); // produces 404 error when page not found
$d->addFilter(new PublicPageDispatchFilter()); // produces 403 error if not logged in while accessing non-public page
$d->addFilter(new AdminDispatchFilter()); // produces 403 error if insufficient access level
$d->addFilter(new PageTitleDispatchFilter()); // picks up page title and injects into HTML head
$d->addFilter(new PageTemplateDispatchFilter()); // picks up page template and injects view instance into controller

LIVE or Debug::log("Prefiltering", Debug::DEBUG);
$d->preFilter();
LIVE or Debug::log("Dispatch and execution", Debug::DEBUG);
try {
	$d->dispatch();
} catch (Exception $e) {
	Debug::log($e->getMessage() . PHP_EOL . $e->getTraceAsString(), Debug::ERROR);
	PageView::getInstance()->displayError("Action could not be completed due to software problem.");
}
LIVE or Debug::log("Postfiltering", Debug::DEBUG);
$d->postFilter();

$page = PageView::getInstance();
$page->isEnabled() and print($page->__toString());