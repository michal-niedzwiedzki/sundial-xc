<?php

$cErr = cError::getInstance();
$cErr->SaveErrors();

if (!isset($redir_url)) {
	isset($_GET['location']) and $redir_url = $_GET['location'];
	isset($_POST['location']) and $redir_url = $_POST['location'];
} elseif (!isset($redir_type)) {
	isset($_GET['type']) and $redir_type = $_GET['type'];
} elseif (!isset($redir_item)) {
	isset($_GET['item']) and $redir_item = $_GET['item'];
}

if (isset($redir_url)) {
	// a specific URL was requested.  Go there regardless of other variables.
	header("Location:".$redir_url);
} elseif (isset($redir_type) and isset($redir_item)) {
	header("Location: ".HTTP_BASE.$GLOBALS['SITE_SECTION_URL'][$redir_type]."?item=".$redir_item);
} elseif (isset($redir_type)) {
	// $item not specified
	header("Location: ".HTTP_BASE.$GLOBALS['SITE_SECTION_URL'][$redir_type]);
} else {
	// dunno where to go.  Go home.
	header("Location: ".HTTP_BASE);
}

exit;