<?php
include_once("includes/inc.global.php");

cMember::getCurrent()->MustBeLoggedOn();
$p->site_section = PROFILE;
$p->page_title = "Desactivar listado por vacaciones";

include("classes/class.directory.php");
$form = FormHelper::standard();

if($_REQUEST["mode"] == "admin") {
	$user->MustBeLevel(1);
	$form->addElement("hidden","mode","admin");
	$member = new cMember();
	$member->LoadMember($_REQUEST["member_id"]);
	$text = "Esta función desactiva temporalmente el listado de servicios de un soci@.";
	$pronoun = "they";
} else {
	cMember::getCurrent()->MustBeLoggedOn();
	$member = $user;
	$form->addElement("hidden","mode","self");
	$text = "Esta función desactiva temporalmente tu listado de servicios. ";
	$pronoun = "you";
}

$text .= "Cuando estan desactivados no aparecen en la aplicación. En la fecha seleccionada volverán a estar visibles.";
$form->addElement("static", null, $text, null);
$form->addElement("hidden","member_id", $member->member_id);
$form->addElement("static", null, null, null);
$today = getdate();
$options = array('language'=> 'es', 'format' => 'dFY', 'minYear' => $today['year'],'maxYear' => $today['year']+5);
$form->addElement("date", "return_date", "Fecha de reactivación?", $options);
$form->addElement("static", null, null, null);
$form->addElement("submit", "btnSubmit", "Desactivar");

$form->registerRule('verify_future_date','function','verify_future_date');
$form->addRule('return_date','Tienes que elegir una fecha futura','verify_future_date');
$form->registerRule('verify_valid_date','function','verify_valid_date');
$form->addRule('return_date','Fecha is invalida','verify_valid_date');

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $p, $member;
	
	$date = $values['return_date'];
	$return_date = new cDateTime($date['Y'] . '/' . $date['F'] . '/' . $date['d']);
	
	$listings = new cListingGroup(OFFER_LISTING);
	$listings->LoadListingGroup(null,"%",$member->member_id);
	$listings->InactivateAll($return_date);
	
	$listings = new cListingGroup(WANT_LISTING);
	$listings->LoadListingGroup(null,"%",$member->member_id);
	$listings->InactivateAll($return_date);
	
	$output="El listado ha sido desactivado con exito.";
	
	$p->DisplayPage($output);
}

function verify_future_date ($element_name,$element_value) {
	$today = getdate();
	$date = $element_value;
	$date_str = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];

	if (strtotime($date_str) <= strtotime("now")) // date is a past date
		return false;
	else
		return true;
}

function verify_valid_date ($element_name,$element_value) {
	$date = $element_value;
	return checkdate($date['F'],$date['d'],$date['Y']);
}

?>
