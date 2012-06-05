<?php

require_once "../bootstrap.php";

$master = PageView::getInstance();
$master->title = "Noticias";

$output = "";

$news = new cNewsGroup();
$news->LoadNewsGroup();
$newstext = $news->DisplayNewsGroup();
if ($newstext != "") {
	$output .= $newstext;
} else {
	$output .= "Ninguna noticia de momento.";
}

$newsletters = new cUploadGroup("N");

if ($newsletters->LoadUploadGroup()) {
	$output .= "<i>To read the latest ". SITE_SHORT_TITLE . " newsletter, go <a href=\"newsletters.php\">here</a>.</i>";
}

$master->displayPage("<p>$output</p>");
