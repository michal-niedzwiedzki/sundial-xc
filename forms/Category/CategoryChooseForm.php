<?php

final class CategoryChooseForm extends Form {

	public function __construct(array $categories) {
		parent::__construct();
		$this->addElement("select", "category", "Categoría:", $categories);
		$buttons = array(
			HTML_QuickForm::createElement('submit', 'btnEdit', 'Editar'),
			HTML_QuickForm::createElement('submit', 'btnDelete', 'Borrar'),
		);
		$this->addGroup($buttons, NULL, NULL, '&nbsp;');
	}

}