<?php

require_once dirname(__FILE__) . "/../../bootstrap.php";

header("Content-Type: text/html; charset=UTF-8");

// check maintenance flag
if (Config::getInstance()->site->maintenance) {
	header("HTTP/1.4 503 Service unavailable");
	echo "<html><head></head><body><h2>Service has been taken off-line for maintenance</h2><p>Apologies for inconveniences, please come back later.</p><pre>HTTP/1.1 503 Service unavailable</pre></body></html>";
	die();
}

// set up dispatcher and filtering
$d = Dispatcher::getInstance();
LIVE or $d->addFilter(new DebugViewDispatchFilter());
LIVE or $d->addFilter(new RequestDebugDispatchFilter());
$d->addFilter(new PathInfoDispatchFilter());
$d->addFilter(new PublicPageDispatchFilter());
$d->addFilter(new AccessLevelDispatchFilter());
$d->addFilter(new PageTitleDispatchFilter());
$d->addFilter(new PageTemplateDispatchFilter());

Debug::log("Prefiltering", Debug::DEBUG);
$d->preFilter();
Debug::log("Dispatch and execution", Debug::DEBUG);
try {
	$d->dispatch();
} catch (Exception $e) {
	if (!Config::getInstance()->live) {
		Debug::log($e->getMessage() . PHP_EOL . $e->getTraceAsString(), Debug::ERROR);
	}
	PageView::getInstance()->displayError("Action could not be completed due to software problem.");
}
Debug::log("Postfiltering", Debug::DEBUG);
$d->postFilter();

echo PageView::getInstance()->__toString();