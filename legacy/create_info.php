<?php

require_once "../bootstrap.php";

$user = cMember::getCurrent();
$user->MustBeLevel(1);

// First, we define the form
$form = FormHelper::standard();
$form->addElement("text", "title", "Title", array("size" => 35, "maxlength" => 100));
$form->addElement("textarea", "description", "Content", array("cols"=>65, "rows"=>5, "wrap"=>"soft"));
$form->addElement("submit", "btnSubmit", "Submit");

// Set up validation rules for the form
$form->addRule("title","Enter a title","required");
$form->addRule("description","Enter some body text","required");

// Then check if we are processing a submission or just displaying the form
if ($form->validate()) { // Form is validated so processes the data
	$form->freeze();
	$form->process("process_data", false);
}

$master = PageView::getInstance();
$master->title = "Create a New Information Page";
$master->displayPage($form->toHtml());

// The form has been submitted with valid data, so process it
function process_data ($values) {
	$insert = array(
		"date" => time(),
		"title" => $values["title"],
		"body" => $values["description"],
	);
	$out = PDOHelper::insert(DB::PAGES, $insert);
	if ($out)
		PageView::getInstance()->setMessage("New information page added.");
	else
		PageView::getInstance()->setMessage("There was a problem adding the new page.");
}
