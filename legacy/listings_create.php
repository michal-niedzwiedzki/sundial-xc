<?php

require_once "../bootstrap.php";

cMember::getCurrent()->MustBeLoggedOn();

$master = PageView::getInstance();
$master->title = "Create Listings";
$master->displayPage(new View("listings_create.phtml"));
