<?php
include_once("includes/inc.global.php");
$p->site_section = SITE_SECTION_OFFER_LIST;
	
$user->MustBeLevel(2);
$form = FormHelper::standard();

$form->addElement("header", null, "Choose Member whose Photo you wish to Edit");
$form->addElement("html", "<TR></TR>");

$ids = new cMemberGroup;
$ids->LoadMemberGroup(null,true);

$form->addElement("select", "member_id", "Member", $ids->MakeIDArray());
$form->addElement("static", null, null, null);
$form->addElement('submit', 'btnSubmit', 'Upload/Replace Photo');

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	$user = cMember::getCurrent();
	header("location:http://".HTTP_BASE."/member_photo_upload.php?mode=admin&member_id=".$values["member_id"]);
	exit;	
}

?>
