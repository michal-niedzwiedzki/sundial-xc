<?php

cError::getInstance()->SaveErrors();

if (!isset($redir_url)) {
	isset($_GET["location"]) and $redir_url = $_GET["location"];
	isset($_POST["location"]) and $redir_url = $_POST["location"];
} elseif (!isset($redir_type)) {
	isset($_GET["type"]) and $redir_type = $_GET["type"];
} elseif (!isset($redir_item)) {
	isset($_GET["item"]) and $redir_item = $_GET["item"];
}

if (isset($redir_url)) {
	$to = $redir_url;
} elseif (isset($redir_type) and isset($redir_item)) {
	$to = HTTP_BASE . $GLOBALS["SITE_SECTION_URL"][$redir_type] . "?item=" . $redir_item;
} elseif (isset($redir_type)) {
	$to = HTTP_BASE . $GLOBALS["SITE_SECTION_URL"][$redir_type];
} else {
	$to = HTTP_BASE;
}

if (Debug::hasProblems()) {
	Debug::log("Problems exist that prevent redirect to {$to}", Debug::INFO);
 	return;
}

header("Loadtion: $to");
exit;