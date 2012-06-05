<?php

require_once "../bootstrap.php";

$user = cMember::getCurrent();
$user->MustBeLevel(1);

$form = FormHelper::standard();
$form->addElement("header", null, "Elegir soci@ para editar");
$form->addElement("html", "<TR></TR>");

$ids = new cMemberGroup;
$ids->LoadMemberGroup(null,true);

$form->addElement("select", "member_id", "Soci@", $ids->MakeIDArray());
$form->addElement("submit", "btnSubmit", "Editar");

if ($form->validate()) { // Form is validated so processes the data
	$form->freeze();
	$form->process("process_data", false);
}

$master = PageView::getInstance();
$master->displayPage($form->toHtml());

function process_data ($values) {
	$user = cMember::getCurrent();
	header("Location: ".HTTP_BASE."/member_edit.php?mode=admin&member_id=".$values["member_id"]);
	exit;
}