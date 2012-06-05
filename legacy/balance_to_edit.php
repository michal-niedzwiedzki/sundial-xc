<?php

include_once "includes/inc.global.php";
$p->site_section = SITE_SECTION_OFFER_LIST;
$user->MustBeLevel(1);
include "includes/inc.forms.php";
$config = Config::getInstance();

if (!$config->legacy->OVRIDE_BALANCES) {
	// Provision for overriding member balances has been turned off, return to the admin menu
	header("location:http://".HTTP_BASE."/admin_menu.php");
	exit;
}

$form->addElement("header", null, "Seleccionar soci@ para cambio de saldo");
$form->addElement("html", "<TR></TR>");

$ids = new cMemberGroup;
$ids->LoadMemberGroup(null,true);

$form->addElement("select", "member_id", "Soci@", $ids->MakeIDArray());
$form->addElement("static", null, null, null);
$form->addElement('submit', 'btnSubmit', 'Editar Saldo');

if ($form->validate()) { // Form is validated so processes the data
	$form->freeze();
	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data($values) {
	header("Location: http://".HTTP_BASE."/edit_balance.php?mode=admin&member_id=".$values["member_id"]);
	exit;
}
