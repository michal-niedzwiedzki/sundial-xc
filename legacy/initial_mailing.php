<?php

include_once("includes/inc.global.php");

$user->MustBeLevel(2);
$p->site_section = ADMINISTRATION;
$p->page_title = "Initial Mailing for Rollout";

$subject = "Mensaje importante del " . SITE_LONG_TITLE;
/*
$message = "Hola,\n\nLa nueva página web para el " . SITE_LONG_TITLE . " esta activa!  Ahora puedes ver los servicios ofrecidos, modificar tus propios servicios o crear algunos nuevos. También puedes registrar tus transacciones con otr@s soci@s.\n\nla dirección de la página es http://www.bancodetiempomalasana.com) y tu id de soci@ y contraseña estan incluidos en este mensaje. La contraseña ha sido generada por la aplicación, y puedes cambiarla después de entrar en la aplicación.";
*/

$message = "";

$all_members = new cMemberGroup();
$all_members->LoadMemberGroup();

$output = "";

foreach ($all_members->members as $member) {
	if ($member->member_id == 'francis' or $member->member_id == 'lia')
		continue;
	
	$password = $member->GeneratePassword();
	$changed = $member->ChangePassword($password);
	
	if(!$changed) {
		$output .= "Could not reset password for '". $member->member_id ."'. Skipped email.<BR>";
		continue;
	}

// $member->person[0]->email
	$mailed = mail($member->person[0]->email, $subject, $message . "\n\nMember ID: ". $member->member_id ."\n". "Password: ". $password, EMAIL_FROM);

	if(!$mailed)
		$output .= "Could not email ". $member->member_id .".  His/her password is '". $password ."'.<BR>";
}

if($output == "")
	$output = "Email successfully sent to all members.";

$p->DisplayPage($output);



?>
