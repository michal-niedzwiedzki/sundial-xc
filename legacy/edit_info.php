<?php

require_once "../bootstrap.php";

$user = cMember::getCurrent();
$user->MustBeLevel(1);

$form = FormHelper::standard();

$pgs = cInfo::LoadPages();
if ($pgs) {
	foreach ($pgs as $pg) {
		$p_array[$pg["id"]] = stripslashes($pg["title"]);
	}
	$form->addElement("select", "news_id", "Which Info Page?", $p_array);
	$form->addElement("static", null, null, null);
	$form->addElement('submit', 'btnSubmit', 'Edit');
} else {
	$form->addElement("static", null, "There are no current Info Pages.", null);
}


if ($form->validate()) {
	// form is validated so processes the data
	$form->freeze();
 	$form->process("process_data", false);
}

// display the form
$master = PageView::getInstance();
$master->title = "Choose Info Page to Edit";
$master->displayPage($form->toHtml());

function process_data ($values) {
	header("location:http://".HTTP_BASE."/do_info_edit.php?id=".$values["news_id"]);
	exit;
}