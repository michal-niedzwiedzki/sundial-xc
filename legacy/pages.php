<?php

include_once("includes/inc.global.php");
include("classes/class.info.php");

if ($_GET["destroySess"]==1) {
	
	if ($_GET["confirm"]==1) {
		session_destroy();
		echo "Session Destroyed. <a href=index.php>Continue</a>";
	}
	else {
		echo "Really Destroy Session? <a href=pages.php?destroySess=1&confirm=1>Yes</a> | <a href=javascript:history.back(1)>No (Go back)</a>";
	}
	
	exit;
}

$pg = cInfo::LoadOne($_REQUEST["id"]);

if ($pg["permission"]==3) // Admin
	$user->MustBeLevel(2);
else if ($pg["permission"]==2) // Committe
	$user->MustBeLevel(1);
else if ($pg["permission"]==1) // Members
	cMember::getCurrent()->MustBeLoggedOn();

$p->site_section = SECTION_INFO;

if ($_REQUEST["printer_view"]!=1 || !$user->IsLoggedOn()) { 
	print $p->MakePageHeader();
	print $p->MakePageMenu();
}
else {
	
	print '<head><link rel="stylesheet" href="http://'. HTTP_BASE .'/print.css" type="text/css"></link></head>';
}
	
if (!$pg) {
	$p->page_title = "Oops, you have requested a non-existant page.";
	print $p->MakePageTitle();

}
else {
	$p->page_title = stripslashes($pg["title"]);
	
	if ($user->member_role>0)
		$p->page_title .= ' <br><font size=1><a href=do_info_edit.php?id='.$_REQUEST["id"].'>[Edit]</a></font>';
	
	print $p->MakePageTitle();
	print stripslashes($pg["body"]);
}

if ($_REQUEST["printer_view"]!=1 || $user->member_role<1) { 
	print $p->MakePageFooter();
}
?>