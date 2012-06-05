<?php

require_once "../bootstrap.php";

$user = cMember::getCurrent();

$master = PageView::getInstance();
$master->title = "Editar categoría";

$form = FormHelper::standard();

//
// Define form elements
//
$user->MustBeLevel(1);

$category = new cCategory();
$category->LoadCategory($_REQUEST["category_id"]);
$form->addElement("hidden", "category_id", $_REQUEST["category_id"]);
$form->addElement("text", "category", "Nombre de la categoría", array("size" => 30, "maxlength" => 40));
$form->addElement('submit', 'btnSubmit', 'Guardar');

//
// Define form rules
//
$form->addRule('category', 'Es obligatorio insertar una nombre', 'required');

//
// Then check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
	$form->freeze();
	$form->process("process_data", false);
} else {
	$form->setDefaults(array("category"=>$category->description));
	$master->displayPage($form->toHtml());  // just display the form
}

function process_data ($values) {
	global $cErr, $category;
	$category->description = $values["category"];
	if ($category->SaveCategory()) {
		$output = "La categoría ha sido actualizada.";
	} else {
		$output = "No ha sido posible guardar los cambios. Intentalo otra vez mas tarde.";
	}
	PageView::getInstance()->displayPage($output);
}
