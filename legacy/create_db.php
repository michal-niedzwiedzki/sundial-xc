<?php

require "../bootstrap.php";

if (!DB::hasInnoDB()) {
	die("Your database does not have InnoDB support. See the installation instructions for more information about InnoDB. Installation aborted.");
}

DB::create();
DB::initializeSystemAccount();
DB::initializeCategories();

$master = PageView::getInstance();
$master->displayPage("Database has been created. Click <a href=\"member_login.php\">here</a> to login.");
