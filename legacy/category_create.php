<?php

include_once("includes/inc.global.php");

$p->site_section = LISTINGS;
$p->page_title = "Crear una nueva categoría de servicios";

$form = FormHelper::standard();
include_once("classes/class.category.php");

//
// Define form elements
//
$user->MustBeLevel(1);

$form->addElement("text", "category", "Nombre de la categoría", array("size" => 30, "maxlength" => 40));
$form->addElement("static", null, null, null);

$form->addElement('submit', 'btnSubmit', 'Insertar');

//
// Define form rules
//
$form->addRule('category', 'Tienes que insertar un nombre para la categoría', 'required');

//
// Then check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {
   $p->DisplayPage($form->toHtml());  // just display the form
}

function process_data ($values) {
	global $p;
	
	$category = new cCategory($values["category"]);
	
	if ($category->SaveNewCategory()) {
		$output = "La nueva categoría ha sido creada.";
	} else {
		$output = "No ha sido posible crear la nueva categoría.  Intentalo otra vez mas tarde.";
	}
	
	$p->DisplayPage($output);
}

//
// Form rule validation functions
//


?>
