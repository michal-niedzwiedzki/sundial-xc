<?php

require_once dirname(__FILE__) . "/../../bootstrap.php";

ini_set('display_errors', "On");
error_reporting(E_ALL | E_STRICT);

$d = Dispatcher::getInstance();
$d->setControllerDir("controllers/rest/v0.0");
$d->addFilter(new RestRouteDispatchFilter());
$d->addFilter(new ContentTypeDispatchFilter());

$d->preFilter();
$d->dispatch();
$d->postFilter();

echo $d->getController()->page;
