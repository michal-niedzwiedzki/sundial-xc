<?php

final class CategoryEditForm extends Form {
	
	public function __construct($id = NULL) {
		parent::__construct();
		$form->addElement("hidden", "category_id", $id);
		$this->addElement("text", "category", "Nombre de la categoría", array("size" => 30, "maxlength" => 40));
		$this->addElement('submit', 'btnSubmit', 'Guardar');
		$this->addRule('category', 'Tienes que insertar un nombre para la categoría', 'required');
	}

}