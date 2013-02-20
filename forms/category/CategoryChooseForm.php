<?php

final class CategoryChooseForm extends Form {

	public function __construct(array $categories) {
		parent::__construct();
		$this->addElement("select", "category", "(field)Category", $categories);
		$buttons = array(
			HTML_QuickForm::createElement("submit", "btnEdit", "(button)Edit"),
			HTML_QuickForm::createElement("submit", "btnDelete", "(button)Delete"),
		);
		$this->addGroup($buttons, NULL, NULL, "&nbsp;");
	}

}