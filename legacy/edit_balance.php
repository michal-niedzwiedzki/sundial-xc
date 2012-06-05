<?php

include_once("includes/inc.global.php");

$p->site_section = SITE_SECTION_OFFER_LIST;

$form = FormHelper::standard();
	

//
// First, we define the form
//

$user->MustBeLevel(1);

if (OVRIDE_BALANCES!=true) // Provision for overriding member balances has been turned off, return to the admin menu
	header("location:http://".HTTP_BASE."/admin_menu.php");
	
$member = new cMember;
$member->LoadMember($_REQUEST["member_id"]);
	
$form->addElement("header", null, "Editar saldo de soci@: " . $member->person[0]->first_name . " " . $member->person[0]->mid_name);
$form->addElement("hidden","member_id",$_REQUEST["member_id"]);
$form->addElement("text", "balance1", "Valor ahora", array("size" => 3, "maxlength" => 4));
$form->addElement("text", "balance2", "Valor nuevo", array("size" => 3, "maxlength" => 4));	
$form->addElement('submit', 'btnSubmit', 'Actualizar Saldo');

$balance = explode(".",$member->balance);

$current_values["balance1"] = $balance[0];
$current_values["balance2"] = $balance[1];
$form->setDefaults($current_values);
//
// Check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Otherwise we need to load the existing values
	$member = new cMember;
	if($_REQUEST["mode"] == "admin") {
        $user->MustBeLevel(1);
		$member->LoadMember($_REQUEST["member_id"]);
    }
	else {
		$member = $user;
    }
			
   $p->DisplayPage($form->toHtml());  // display the form
}

//
// The form has been submitted with valid data, so process it   
//
function process_data ($values) {
	global $p, $user,$cErr, $cDB;
	
	//$balance = trim($values["balance1"]).".".trim($values["balance2"]);
	$balance = $values["balance2"];
	
	$q = 'UPDATE member set balance='.$cDB->EscTxt($balance).' where member_id='.$cDB->EscTxt($values["member_id"]).'';
//	echo $q;
	$success = $cDB->Query($q);
	
	if ($success)
		$output = "El saldo de este soci@ ha sido cambiado a: '$balance'. <a href=balance_to_edit.php>Editar el saldo de otro soci@?</a>";
	else
		$output = "Ha ocurrido un problema actualizando los datos.";
		
	$p->DisplayPage($output);
}
