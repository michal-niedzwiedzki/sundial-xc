<?php

final class ListingCreateForm extends Form {

	protected $userId;

	public function __construct($type, $userId, $adminMode = FALSE) {
		parent::__construct();
		$this->userId = $userId;

		if ($adminMode) {
			$this->addElement("hidden", "mode", "admin");
			if ($userId) {
				$this->addElement("hidden", "user_id", $userId);
			} else {
				$ids = new cMemberGroup;
				$ids->LoadMemberGroup();
				$this->addElement("select", "user_id", "ID de Socio", $ids->MakeIDArray());
			}
		} else {
			$this->addElement("hidden", "user_id", $userId);
			$this->addElement("hidden", "mode", "self");
		}

		$categories = Category::getAll();
		$categoriesList = array_map(function(Category $c) { return $c->description; }, $categories);

		$this->addElement("hidden", "type", $type);
		$this->addElement("text", "title", "Nombre", array("size" => 30, "maxlength" => 60));
		$this->addElement("select", "category", "Categoría", $categoriesList);
		$this->addElement("textarea", "description", "Descripción", array("cols" => 45, "rows" => 5, "wrap" => "soft"));
		$this->addElement("submit", "btnSubmit", "Insertar");

		// add rules
		$this->addRule("title", "Insertar un nombre", "required");
		$this->registerRule("verify_not_duplicate", "function", "verifyNotDuplicate", $this);
		$this->addRule("title", "Ya tienes un servicio con ese nombre", "verifyNotDuplicate");
		$this->registerRule("verifyCategory", "function", "verifyCategory", $this);
		$this->addRule("category", "Seleccione una categoría", "verifyCategory");
	}

	public function verifyNotDuplicate($value) {
		$titleList = new cTitleList($type);
		$titles = $titleList->MakeTitleArray($this->userId);
		foreach ($titles as $title) {
			if ($value == $title) {
				return FALSE;
			}
		}
		return TRUE;
	}

	public function verifyCategory($value) {
		return $value and NULL !== Category::getById($value);
	}

}