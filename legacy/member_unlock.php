<?php
include_once("includes/inc.global.php");

$user->MustBeLevel(1);
$p->site_section = ADMINISTRATION;
$p->page_title = "Desbloquear cuenta y crear contraseña nueva";

$form = FormHelper::standard();

$form->addElement("static", 'contact', "Aqui se puede desbloquear una cuenta y crear un contraseña nueva. Si es posible la nueva contraseña se envia al soci@. En caso de no tener correo hay que avisar el soci@ directamente.", null);
$form->addElement("static", null, null);
$ids = new cMemberGroup;
$ids->LoadMemberGroup();
$form->addElement("select", "member_id", "Seleccionar cuenta", $ids->MakeIDArray());

$form->addElement("static", null, null, null);
$form->addElement("submit", "btnSubmit", "Desbloquear");
$form->addElement("radio", "emailTyp", "", "Enviar correo con nueva contraseña","pword");
$form->addElement("radio", "emailTyp", "", "Mostrar la contraseña nueva en pantalla","show_pword");
//$form->addElement("radio", "emailTyp", "", "Enviar correo de bienvenida","welcome");

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $p;
	
	$list = "";
	$member = new cMember;
	$member->LoadMember($values["member_id"]);

	if($consecutive_failures = $member->UnlockAccount()) {
		$list .= "Esta cuenta estaba bloqueada debido a ". $consecutive_failures ." intentos fallidos de entrar. Ahora esta activa otra vez.";
	}


	$password = $member->GeneratePassword();
	$member->ChangePassword($password); // This will bomb out if the password change fails
	
	$list .= "La nueva contraseña ha sido generada";
	
	$whEmail = "'Contraseña cambiada'";
	
	if ($_REQUEST["emailTyp"]=='pword') {
    
	  //$mailed = mail($member->person[0]->email, PASSWORD_RESET_SUBJECT, PASSWORD_RESET_MESSAGE . "\n\nID de soci@: ". $member->member_id ."\nNueva contraseña: ". $password, EMAIL_FROM);
		$mailed = $member->esmail($member->person[0]->email, PASSWORD_RESET_SUBJECT, PASSWORD_RESET_MESSAGE . "\n\nID de soci@: ". $member->member_id ."\nNueva contraseña: ". $password);
		if($mailed)
		  $list .= " y un correo de $whEmail ha sido enviado a la dirección del soci@ (". $member->person[0]->email .").";
		else
		  $list .= ". <I>El intento de enviar por correo la nueva contraseña no ha funcionado. Avisa al administrador del banco de tiempo ". PHONE_ADMIN ."</I>.";
    
	}

	else {
	  
	  $list .= " y el valor nuevo es: $password ." ;
	}

		
	$p->DisplayPage($list);

	}
?>
