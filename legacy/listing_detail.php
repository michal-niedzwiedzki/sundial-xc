<?php

require_once "../bootstrap.php";

cMember::getCurrent()->MustBeLoggedOn();

$listing = new cListing();
$listing->LoadListing(HTTPHelper::get("title"), HTTPHelper::get("member_id"), substr(HTTPHelper::get("type"), 0, 1));
$output = $listing->DisplayListing();

$master = PageView::getInstance();
$master->title = HTTPHelper::get("title");
$master->displayPage($output);

include "includes/inc.events.php";