<?php

include_once("includes/inc.global.php");
include("classes/class.info.php");
$form = FormHelper::standard();

$user->MustBeLevel(1);

$p->site_section = EVENTS;

$p->page_title = "Information Page URL's (for linking purposes)";

$pgs = cInfo::LoadPages();

$output .= "<table width=80%><tr><td width=50%><b>Page</b></td><td width=50%><b>URL</b></td></tr>";

foreach($pgs as $pg) {
		
	$output .=  "<tr><td><a href=pages.php?id=".$pg["id"].">".stripslashes($pg["title"])."</a></td><td>pages.php?id=".$pg["id"]."</td></tr>";
}

$output .= "</table>";

$p->DisplayPage($output);
