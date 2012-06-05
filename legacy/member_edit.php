<?php

require_once "../bootstrap.php";

$user = cMember::getCurrent();
$master = PageView::getInstance();
$form = FormHelper::standard();
$page = new View("pages/member/edit.phtml");
$page->form = $form;
//
// First, we define the form
//
if($_REQUEST["mode"] == "admin") {  // Administrator is editing a member's account
	$user->MustBeLevel(1);
	$member = new cMember;
	$member->LoadMember($_REQUEST["member_id"]);
	$page->title = "Editar soci@ " . $member->person[0]->first_name . " " . $member->person[0]->mid_name;

	$form->addElement("hidden","mode","admin");
	$form->addElement("hidden","member_id",$_REQUEST["member_id"]);
	if($_REQUEST["member_id"] == "ADMIN") {
		$form->addElement("hidden","member_role","9");
	} else {
		$form->addElement("select", "member_role", "Tipo de soci@", array("0"=>"Soci@", "1"=>"Gestión", "2"=>"Administrador"));
	}
	//$acct_types = array("S"=>"Single", "J"=>"Joint", "H"=>"Household", "O"=>"Organization", "B"=>"Business", "F"=>"Fund");
	//$form->addElement("select", "account_type", "Account Type", $acct_types);
	$form->addElement("static", null, "Comentario del administrador", null);
	$form->addElement("textarea", "admin_note", null, array("cols"=>45, "rows"=>2, "wrap"=>"soft", "maxlength" => 100));
	$today = getdate();
	$options = array("language"=> "es", "format" => "dFY", "minYear"=>JOIN_YEAR_MINIMUM, "maxYear"=>	$today["year"]);
	$form->addElement("date", "join_date",	"Fecha de inscripción", $options);
	//$form->addElement("text", "mother_mn", "Mother's Maiden Name", array("size" => 20, "maxlength" => 30));
	$form->addElement("static", null, null, null);
	$form->addElement("text", "first_name", "Nombre", array("size" => 15, "maxlength" => 20));
	$form->addElement("text", "mid_name", "Primer Apellido", array("size" => 20, "maxlength" => 30));
	$form->addElement("text", "last_name", "Segundo Apellido", array("size" => 20, "maxlength" => 30));
	$form->addElement("text", "fax_number", "DNI o Pasaporte", array("size" => 20, "maxlength" => 30));
	$options = array("language"=> "es", "format" => "dFY", "maxYear"=>$today["year"], "minYear"=>"1900");
	$form->addElement("date", "dob", "Fecha de nacimiento", $options);
	$form->addElement("select", "sex", "Sexo:", array("1"=>"F", "2"=>"M"));
	$form->addElement("static", null, null, null);
	$form->addElement("text", "address_street1", 'Dirección linea 1', array("size" => 25, "maxlength" => 30));
	$form->addElement("text", "address_street2", 'Dirección linea 2', array("size" => 25, "maxlength" => 30));
	$update_text="Frecuencia de actualizaciones por correo electronico";
	$update2_text="¿Debe confirmar los pagos que recibe?";
} else {  // Member is editing own profile
	cMember::getCurrent()->MustBeLoggedOn();
	$page->title = "Editar Perfil";
	$form->addElement("html", "<TR></TR>");
	$form->addElement("hidden","member_id", $user->member_id);
	$form->addElement("hidden","mode","self");
	$update_text="¿Con que frecuencia quieres recibir actualizaciones por correo electronico?";
	$update2_text="¿Quieres confirmar los pagos que recibes?";
}


$form->addElement("text", "email", "Dirección de correo", array("size" => 25, "maxlength" => 40));
$form->addElement("text", "phone1", "Primer teléfono", array("size" => 20));
$form->addElement("text", "phone2", "Segundo teléfono", array("size" => 20));
//$form->addElement("text", "fax", "Fax Number", array("size" => 20));
$form->addElement("static", null, null, null);
$frequency = array("0"=>"Nunca", "1"=>"Cada día", "7"=>"Cada semana", "30"=>"Cada mes");
$form->addElement("select", "email_updates", $update_text, $frequency);

$confirmP = array("0"=>"Aceptar todos", "1"=>"Confirmar");
$form->addElement("select", "confirm_payments", $update2_text, $confirmP);
$form->addElement("static", null, null, null);

//$form->addElement("text", "address_street1", ADDRESS_LINE_1, array("size" => 25, "maxlength" => 30));
//$form->addElement("text", "address_street2", ADDRESS_LINE_2, array("size" => 25, "maxlength" => 30));
//$form->addElement("text", "address_city", ADDRESS_LINE_3, array("size" => 25, "maxlength" => 50));

// TODO: The State and Country codes should be Select Menus, and choices should be built
// dynamically using an internet database (if such exists).
//$form->addElement("text", "address_state_code", STATE_TEXT, array("size" => 25, "maxlength" => 50));
//$form->addElement("text", "address_post_code", ZIP_TEXT, array("size" => 10, "maxlength" => 20));
//$form->addElement("text", "address_country", "Country", array("size" => 25, "maxlength" => 50));

/*[chris] Personal Profile bits */

/*if (SOC_NETWORK_FIELDS==true) {

	$form->addElement("static", null, null, null);
	$form->addElement("select", "age", "Age", $agesArr);
	$form->addElement("select", "sex", "Sex", $sexArr);
	$form->addElement("textarea", "about_me", 'About Me', array("cols"=>45, "rows"=>5, "wrap"=>"soft", "maxlength" => 300));
	}*/

$form->addElement("static", null, null, null);
$form->addElement('submit', 'btnSubmit', 'Actualizar');

//
// Define form rules
//
$form->addRule('member_id',  'ID de soci@ obligatorio', 'required');
$form->addRule('password', 'La contraseña debe tener al menos 7 caracteres', 'minlength', 7);
$form->addRule('first_name', 'Enter a first name', 'required');
$form->addRule('mid_name', 'Insertar al menos un apellido', 'required');
$form->addRule('fax_number', 'Insertar identificación', 'required');
/*$form->addRule('address_city', 'Enter a ' . ADDRESS_LINE_3, 'required');
$form->addRule('address_state_code', 'Enter a ' . STATE_TEXT, 'required');
$form->addRule('address_post_code', 'Enter a '.ZIP_TEXT, 'required');
$form->addRule('address_country', 'Enter a country', 'required');*/

$form->registerRule('verify_role_allowed','function','verify_role_allowed');
$form->addRule('member_role','No tienes permisos para asignar un nivel de acceso tan alto','verify_role_allowed');
$form->registerRule('verify_role_allowed1', 'function','verify_role_allowed1');
$form->addRule('member_role', 'No tienes permiso para modificar el nivel de acceso de esta cuenta', 'verify_role_allowed1');

$form->registerRule('verify_not_future_date','function','verify_not_future_date');
$form->addRule('join_date', 'La fecha de inscripción no puede ser una del futuro', 'verify_not_future_date');
//$form->addRule('dob', 'Birth date cannot be in the future', 'verify_not_future_date');
//$form->registerRule('verify_reasonable_dob','function','verify_reasonable_dob');
//$form->addRule('dob', 'A little young, don\'t you think?', 'verify_reasonable_dob');
$form->registerRule('verify_valid_email','function', 'verify_valid_email');
$form->addRule('email', 'Dirección de correo inválida', 'verify_valid_email');
//$form->registerRule('verify_phone_format','function','verify_phone_format');
//$form->addRule('phone1', 'Phone format invalid', 'verify_phone_format');
//$form->addRule('phone2', 'Phone format invalid', 'verify_phone_format');
//$form->addRule('fax', 'Phone format invalid', 'verify_phone_format');


//
// Check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
	$form->freeze();
 	$form->process("process_data", false);
} else {  // Otherwise we need to load the existing values
	$member = new cMember();
	if (HTTPHelper::rq("mode") == "admin") {
        	$user->MustBeLevel(1);
		$member->LoadMember(HTTPHelper::rq("member_id"));
	} else {
		$member = $user;
   	}
	$current_values = array (
		"member_id" => $member->member_id,
		"first_name" => $member->person[0]->first_name,
		"mid_name" => $member->person[0]->mid_name,
		"last_name" => $member->person[0]->last_name,
		"email" => $member->person[0]->email,
		"phone1" => $member->person[0]->phone1_number,
		"phone2" => $member->person[0]->phone2_number,
		"fax_number" => $member->person[0]->fax_number,
		"email_updates" => $member->email_updates,
		"address_street1" => $member->person[0]->address_street1,
		"address_street2" => $member->person[0]->address_street2,
		"address_city" => $member->person[0]->address_city,
		"address_state_code" => $member->person[0]->address_state_code,
		"address_post_code" => $member->person[0]->address_post_code,
		"address_country" => $member->person[0]->address_country,
		"age" => $member->person[0]->age,
		"sex" => $member->person[0]->sex,
		"about_me" => $member->person[0]->about_me,
		"confirm_payments" => @$member->confirm_payments
	);

	// Load defaults for extra fields visible by administrators
	if (HTTPHelper::rq("mode") == "admin") {
        	$user->MustBeLevel(1);
		$current_values["member_role"] = $member->member_role;
		$current_values["account_type"] = $member->account_type;
		$current_values["admin_note"] = $member->admin_note;
		$current_values["join_date"] = array ('d'=>substr($member->join_date,8,2),'F'=>date('n',strtotime($member->join_date)),'Y'=>substr($member->join_date,0,4));
		$current_values["mother_mn"] = $member->person[0]->mother_mn;
		if ($member->person[0]->dob) {
			$current_values["dob"] = array ('d'=>substr($member->person[0]->dob,8,2),'F'=>date('n',strtotime($member->person[0]->dob)),'Y'=>substr($member->person[0]->dob,0,4));  // Using 'n' due to a bug in Quickform
		} else { // If date of birth was left empty originally, display default date
			$today = getdate();
			$current_values["dob"] = array ('d'=>$today['mday'],'F'=>$today['mon'],'Y'=>$today['year']);
		}
	}

	$form->setDefaults($current_values);
	PageView::getInstance()->displayPage($page);
}

//
// The form has been submitted with valid data, so process it
//
function process_data ($values) {

	$today = getdate();

	if ($_REQUEST["mode"] == "admin") {
        cMember::getCurrent()->MustBeLevel(1);
		$member = new cMember();
		$member->LoadMember($_REQUEST["member_id"]);
    } else {
		$member = cMember::getCurrent();
    }

	if ($_REQUEST["mode"] == "admin") {

		$member->confirm_payments = htmlspecialchars($values["confirm_payments"]);
		$member->person[0]->first_name = htmlspecialchars($values["first_name"]);
		$member->person[0]->mid_name = htmlspecialchars($values["mid_name"]);
		$member->person[0]->last_name = htmlspecialchars($values["last_name"]);
		$member->person[0]->address_street1 =
                                htmlspecialchars($values["address_street1"]);
		$member->person[0]->address_street2 =
                                htmlspecialchars($values["address_street2"]);
		$member->person[0]->fax_number = htmlspecialchars($values["fax_number"]);
		$member->member_role = htmlspecialchars($values["member_role"]);
		$member->account_type = htmlspecialchars($values["account_type"]);
		$member->admin_note = htmlspecialchars($values["admin_note"]);
		$member->person[0]->sex = htmlspecialchars($values["sex"]);
		//$member->person[0]->mother_mn = htmlspecialchars($values["mother_mn"]);

		// [chris] fixed problem with passing this ARRAY to htmlspecialchars()...
		$date = $values['join_date'];

		// ... pass to htmlspecialchars() here instead [chris]
		$member->join_date = htmlspecialchars($date['Y'] . '/' . $date['F'] . '/' . $date['d']);

		// [chris] ditto re htmlspecialchars() [see comment above]
		$date = $values['dob'];

		$dob = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];

		// ... pass to htmlspecialchars() here instead [chris]
		$dob = htmlspecialchars($dob);

		if($dob != $today['year']."/".$today['mon']."/".$today['mday']) {
		  $member->person[0]->dob = $dob;
		  } // if date left as default (today's date), we don't want to set it
	}

	$member->confirm_payments = htmlspecialchars($values["confirm_payments"]);

    // TODO: Add ability to temporarily disable an account (vacation) or to
    // disable altogether (left 4th Corner).  Also add ability for user to add
    // a personal note.

	$member->person[0]->email = htmlspecialchars($values["email"]);

	$member->email_updates = htmlspecialchars($values["email_updates"]);

	/*$member->person[0]->address_city =
                                htmlspecialchars($values["address_city"]);
	$member->person[0]->address_state_code =
                                htmlspecialchars($values["address_state_code"]);
	$member->person[0]->address_post_code =
                                htmlspecialchars($values["address_post_code"]);
	$member->person[0]->address_country =
	htmlspecialchars($values["address_country"]);	*/

	/*$phone = new cPhone_uk($values['phone1']);
	$member->person[0]->phone1_area = $phone->area;
	$member->person[0]->phone1_number = $phone->SevenDigits();
	$member->person[0]->phone1_ext = $phone->ext;
	$phone = new cPhone_uk($values['phone2']);
	$member->person[0]->phone2_area = $phone->area;
	$member->person[0]->phone2_number = $phone->SevenDigits();
	$member->person[0]->phone2_ext = $phone->ext;
	$phone = new cPhone_uk($values['fax']);
	$member->person[0]->fax_area = $phone->area;*/
	$member->person[0]->phone1_number = htmlspecialchars($values["phone1"]);
	$member->person[0]->phone2_number = htmlspecialchars($values["phone2"]);

	//$member->person[0]->fax_ext = $phone->ext;

	/*[chris]*/
	/*	if (SOC_NETWORK_FIELDS==true) {

		$member->person[0]->age = htmlspecialchars($values["age"]);
		$member->person[0]->sex = htmlspecialchars($values["sex"]);
		$member->person[0]->about_me = ($values["about_me"]);
	}*/

	$master = PageView::getInstance();
	if ($member->SaveMember()) {
		$master->setMessage("Los cambios han sido guardados");
	} else {
		$master->displayError("Ha occurido un error guardando los cambios. Intentalo otra vez mas tarde");
	}
}
//
// The following functions verify form data
//

function verify_good_member_id ($element_name,$element_value) {
	if(ctype_alnum($element_value)) { // it's good, so return immediately & save a little time
		return true;
	} else {
		$member_id = ereg_replace("\_","",$element_value);
		$member_id = ereg_replace("\-","",$member_id);
		$member_id = ereg_replace("\.","",$member_id);
		if(ctype_alnum($member_id))  // test again now that we've stripped the allowable special chars
			return true;
	}
}


function verify_role_allowed($element_name,$element_value) {
	$user = cMember::getCurrent();
	if($element_value > $user->member_role)
		return false;
	else
		return true;
}


/**
 * You cannot downgrade an account that has higher privileges than you.
 */
function verify_role_allowed1($element_name,$element_value) {
	global $user, $member;

	if (($member->member_role > $user->member_role) &&
          ($element_value != $member->member_role)) {
		return false;
    }
	else {
		return true;
    }
}


function verify_reasonable_dob($element_name,$element_value) {
	global $today;
	$date = $element_value;
	$date_str = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];

	if ($date_str == $today['year']."/".$today['mon']."/".$today['mday'])
		// date wasn't changed by user, so no need to verify it
		return true;
	elseif ($today['year'] - $date['Y'] < 3)  // A little young to be trading, presumably a mistake
		return false;
	else
		return true;
}

function verify_good_password($element_name,$element_value) {
	$i=0; $upper=false; $lower=false; $number=false; $punct=false;
	$length=strlen($element_value);

	while($i<$length) {
		if(ctype_upper($element_value{$i}))
			$upper=true;
		if(ctype_lower($element_value{$i}))
			$lower=true;
		if(ctype_punct($element_value{$i}))
			$punct=true;
		if(ctype_digit($element_value{$i}))
			$number=true;
		$i+=1;
	}

	if($upper and $lower and ($number or $punct))
		return true;
	else
		return false;
}

function verify_no_apostraphes_or_backslashes($element_name,$element_value) {
	if(strstr($element_value,"'") or strstr($element_value,"\\"))
		return false;
	else
		return true;
}

function verify_not_future_date ($element_name,$element_value) {
	$date = $element_value;
	$date_str = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];

	if (strtotime($date_str) > strtotime("now"))
		return false;
	else
		return true;
}

// TODO: This simplistic function should ultimately be replaced by this class method on Pear:
// 		http://pear.php.net/manual/en/package.mail.mail-rfc822.intro.php
function verify_valid_email ($element_name,$element_value) {
	if ($element_value=="")
		return true;		// Currently not planning to require this field
	if (strstr($element_value,"@") and strstr($element_value,"."))
		return true;
	else
		return false;

}

function verify_phone_format ($element_name,$element_value) {
	$phone = new cPhone_uk($element_value);
	return $phone->prefix ? TRUE : FALSE;
}
