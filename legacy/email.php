<?php

include_once("includes/inc.global.php");
$p->site_section = SECTION_EMAIL;
$p->page_title = "Enviar un mensaje a otr@ soci@";

cMember::getCurrent()->MustBeLoggedOn();

$form = FormHelper::standard();

//
// First, we define the form
//

$form->addElement("hidden", "email_to", $_REQUEST["email_to"]);
$form->addElement("hidden", "member_to", $_REQUEST["member_to"]);
$member_to = new cMember;
$member_to->LoadMember($_REQUEST["member_to"]);
$form->addElement("static", null, "Para: <I>". $_REQUEST["email_to"] . " (". $member_to->member_id .")</I>");
$form->addElement("text", "subject", "Asunto: ", array('size' => 35, 'maxlength' => 100));
$form->addElement("select", "cc", "¿Quieres recibir una copia del mensaje?", array("Y"=>"Si", "N"=>"No"));

/*  The following code should work, and works on my server, but not on Open Access.  Bug?
$cc[] =& HTML_QuickForm::createElement('radio',null,null,'<FONT SIZE=2>Yes</FONT>','Y');
$cc[] =& HTML_QuickForm::createElement('radio',null,null,'<FONT SIZE=2>No</FONT>','N');
$form->addGroup($cc, "cc", 'Would you like to recieve a copy?');
*/

$form->addElement("static", null, null, null);
$form->addElement("textarea", "message", "Tu mensaje", array("cols"=>65, "rows"=>10, "wrap"=>"soft"));

$form->addElement("static", null, null, null);
$form->addElement("submit", "btnSubmit", "Enviar");

//
// Define form rules
//
$form->addRule("message", "Inserta tu mensaje", "required");

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$form->setDefaults(array("cc"=>"Y"));
	$p->DisplayPage($form->toHtml());
}

//
// The form has been submitted with valid data, so process it   
//
function process_data ($values) {
	global $p, $user;
	
	if($values["cc"] == "Y") {
		$copy = "$user->person[0]->email .\r\n";
    }
	else {
		$copy = "";
    }

    $headers = "From: .$user->person[0]->email .\r\n";
    
    $headers .= "MIME-Version: 1.0\r\n"; 
    $headers .= "Content-type: text/plain; charset=utf-8\r\n";
    $headers .="Content-Transfer-Encoding: 8bit";
    $mess = wordwrap(htmlspecialchars_decode($values["message"], ENT_QUOTES), 64);

    if(known_email_addressp($_REQUEST["email_to"])) {
      //$mailed = mail($_REQUEST["email_to"], SITE_SHORT_TITLE .": ". $values["subject"], htmlentities($mess), "From:". $user->person[0]->email . $copy);
      $mailed = mail($_REQUEST["email_to"], SITE_SHORT_TITLE .": ". $values["subject"], $mess, $headers);
    }
    else {
        $mailed = false;
    }

	if($mailed) {
		$output = "Tu mensaje ha sido enviado.";
    }
	else {
		$output = "Ha ocurrido un problema enviando el correo.  Intentalo otra vez mas tarde.";	
    }

	$p->DisplayPage($output);
}


/**
 * Checks whether the given email address exists in the database.
 */
function known_email_addressp($email) {
    global $cDB;

    $email = $cDB->EscTxt($email);
    $sql = "SELECT person_id FROM " . DB::PERSONS .
                                                 " WHERE email = $email";
    $r = $cDB->Query($sql);
    if($row = mysql_fetch_array($r)) {
        return true;
    }
    else {
        return false;
    }
}

?>