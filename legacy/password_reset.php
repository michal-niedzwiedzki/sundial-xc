<?php
include_once("includes/inc.global.php");
$p->site_section = SITE_SECTION_OFFER_LIST;

$form = FormHelper::standard();

$form->addElement("header", null, "Recuperar la contraseña");
$form->addElement("html", "<TR></TR>");

$form->addElement("text", "member_id", "Su ID de socio");
$form->addElement("text", "email", "Tu dirección de correo electrónico");

$form->addElement("static", null, null, null);
$form->addElement("submit", "btnSubmit", "Recuperar contraseña");

$form->registerRule('verify_email','function','verify_email');
$form->addRule('email','La dirección de correo no es valida','verify_email');
$form->addElement("static", null, null, null);
$form->addElement("static", 'contact', "Si has olvidado el ID de soci@ o la dirección de correo  <A HREF=contact.php>ponte en contacto</A> con nosotros.", null);

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $p;
	
	$member = new cMember;
	$member->LoadMember($values["member_id"]);

	$password = $member->GeneratePassword();
	$member->ChangePassword($password); // This will bomb out if the password change fails
	$member->UnlockAccount();
	
	$list = "Tu contraseña ha sido cambiada. Puedes cambiarla otra vez si quieres después de entrar en la aplicación.<P>";
	//$mailed = mail($values['email'], PASSWORD_RESET_SUBJECT, PASSWORD_RESET_MESSAGE . "\n\nContraseña Nueva: ". $password, EMAIL_FROM);
	        $mailed = $member->esmail($values['email'], PASSWORD_RESET_SUBJECT, PASSWORD_RESET_MESSAGE . "\n\nContraseña Nueva: ". $password);
	if($mailed)
		$list .= "Una nueva contraseña ha sido enviado a tu dirección de correo.";
	else
		$list .= "<I>El intento de enviar una nueva contraseña ha fallado. Ponte en contacto con nosotros para solucionar el problema</I>.";	
	$p->DisplayPage($list);
}

function verify_email($element_name,$element_value) {
	global $form;
	$member = new cMember;

	if(!$member->VerifyMemberExists($form->getElementValue("member_id")))
		return false;  // Don't want to try to load member if member_id invalid, 
							// because of inappropriate error message.
		
	$member->LoadMember($form->getElementValue("member_id"));

	if($element_value == $member->person[0]->email)
		return true;
	else
		return false;
}

?>