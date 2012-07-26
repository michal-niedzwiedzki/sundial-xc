<?php

final class ListingCreateForm extends Form {

	protected $memberId;
	
	public function __construct($type, $memberId, $adminMode = FALSE) {
		parent::__construct();
		$this->memberId = $memberId;
		
		if ($adminMode) {
			$this->addElement("hidden", "mode", "admin");
			if ($memberId) {
				$this->addElement("hidden", "member_id", $_REQUEST["member_id"]);
			} else {
				$ids = new cMemberGroup;
				$ids->LoadMemberGroup();
				$this->addElement("select", "member_id", "ID de Socio", $ids->MakeIDArray());
			}
		} else {
			$this->addElement("hidden", "member_id", $memberId);
			$this->addElement("hidden", "mode", "self");
		}

		$this->addElement("hidden", "type", $type);
		$this->addElement("text", "title", "Nombre", array("size" => 30, "maxlength" => 60));
		$categoryList = new cCategoryList();
		$this->addElement("select", "category", "Categoría", $categoryList->MakeCategoryArray());
#		USE_RATES
#			? $this->addElement("text", "rate", "Rate", array("size" => 15, "maxlength" => 30))
#			: $this->addElement("hidden", "rate");
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
		$titles = $titleList->MakeTitleArray($this->memberId);
		foreach ($titles as $title) {
			if ($value == $title) {
				return FALSE;
			}
		}
		return TRUE;
	}

	public function verifyCategory($value) {
		return $value != "0";
	}

}