<?php

require_once "../../bootstrap.php";
header("Content-Type: text/html; charset=UTF-8");

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