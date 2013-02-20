<?php

final class CategoryEditForm extends Form {

	public function __construct($id = NULL) {
		parent::__construct();
		$form->addElement("hidden", "category_id", $id);
		$this->addElement("text", "category", "(field)Category title", array("size" => 30, "maxlength" => 40));
		$this->addElement("submit", "btnSubmit", "(button)Save");
		$this->addRule("category", "Category title is required", "required");
	}

}