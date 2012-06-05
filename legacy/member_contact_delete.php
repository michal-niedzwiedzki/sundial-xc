<?php
include_once("includes/inc.global.php");
$p->site_section = PROFILE;
$p->page_title = "Delete Joint Member";

$form = FormHelper::standard();

if($_REQUEST["mode"] == "admin") {
	$user->MustBeLevel(1);
	$form->addElement("hidden","mode","admin");
} else {
	cMember::getCurrent()->MustBeLoggedOn();
	$form->addElement("hidden","mode","self");
}

$person = new cPerson;
$person->LoadPerson($_REQUEST["person_id"]);

$form->addElement("hidden", "person_id", $_REQUEST["person_id"]);
$form->addElement("static", null, "Are you sure you want to permanently delete ". $person->Name() ."?", null);
$form->addElement("static",null,null);
$form->addElement('submit', 'btnSubmit', 'Delete');

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $p, $person;
	
	if($person->DeletePerson())
		$output = "Joint member deleted.";
	else
		$output = "There was an error deleting the joint member.";
		
	$p->DisplayPage($output);
}

?>
