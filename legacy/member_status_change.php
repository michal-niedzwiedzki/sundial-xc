<?php
include_once("includes/inc.global.php");
include_once("classes/class.listing.php");

$user->MustBeLevel(1);
$p->site_section = ADMINISTRATION;

$member = new cMember;
$member->LoadMember($_REQUEST["member_id"]);
if($member->status == 'A')
	$p->page_title = "Desactivar cuenta: ";
else
	$p->page_title = "Reactivar cuenta: ";
	
$p->page_title .= $member->PrimaryName() ." (". $member->member_id .")";

$form = FormHelper::standard();
include_once("classes/class.news.php");

$form->addElement("hidden", "member_id", $_REQUEST["member_id"]);

if($member->status == 'A') {
	$form->addElement("static", null, "¿Estas seguro que quieres desactivar esta cuenta? El soci@ ya no podrá utilizar la aplicación, y todos los actividades que ofrece estarán desactivadas.", null);
	$form->addElement("static", null, null, null);
	$form->addElement('submit', 'btnSubmit', 'Desactivar');
} else {
	$form->addElement("static", null, "¿Estas seguro que quieres reactivar esta cuenta? Hay que reactivar las actividades de la cuenta una por una or crear nuevas.", null);
	$form->addElement("static", null, null, null);
	$form->addElement('submit', 'btnSubmit', 'Reactivar');
}

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $p, $member;
	
	if($member->status == 'A') {
		$success = $member->DeactivateMember();
		$listings = new cListingGroup(OFFER_LISTING);
		$listings->LoadListingGroup(null,null,$member->member_id);
		$date = new cDateTime("yesterday");
		if($success)
			$success = $listings->ExpireAll($date);
		if($success) {
			$listings = new cListingGroup(WANT_LISTING);
			$listings->LoadListingGroup(null,null,$member->member_id);
			$success = $listings->ExpireAll($date);
		}
	} else {
		$success = $member->ReactivateMember();
	}

	if($success)
		$output = "El cambio de estado ha sido guardado.";
	else
		$output = "Ha ocurrido un error actualizando el estado de la cuenta. Intentalo otra vez mas tarde.";	
			
	$p->DisplayPage($output);
}

?>
