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
		$this->addRule("title", "Insertar un nombre", "required");
		$this->registerRule("verify_not_duplicate", "function", "verifyNotDuplicate", $this);
		$this->addRule("title", "Ya tienes un servicio con ese nombre", "verifyNotDuplicate");
		$categoryList = new cCategoryList();
		$this->addElement("select", "category", "Categoría", $categoryList->MakeCategoryArray());
		
		/*if(USE_RATES)
		 $this->addElement("text", "rate", "Rate", array("size" => 15, "maxlength" => 30));
		else
			$this->addElement("hidden", "rate");*/
		
		$this->addElement("static", null, "Descripción", null);
		$this->addElement("textarea", "description", null, array("cols" => 45, "rows" => 5, "wrap" => "soft"));
		$this->addElement("advcheckbox", "set_expire_date", "¿Tiene este servicio fecha limite?");
		$today = getdate();
		$options = array("language"=> "es", "format" => "dFY", "minYear" => $today["year"],"maxYear" => $today["year"]+5, "addEmptyOption"=>"Y", "emptyOptionValue"=>"0");
		$this->addElement("date", "expire_date", "Fecha", $options);
		$this->registerRule("verify_temporary", "function", "verify_temporary");
		//$this->addRule("expire_date", "Temporary listing box must be checked for expiration", "verify_temporary");
		$this->registerRule("verifyFutureDate", "function", "verifyFutureDate", $this);
		$this->addRule("expire_date", "Tiene que ser una fecha futura", "verifyFutureDate");
		$this->registerRule("verifyValidDate", "function", "verifyValidDate", $this);
		$this->addRule("expire_date", "La fecha no es válida", "verifyValidDate");
		$this->registerRule("verifyCategory", "function", "verifyCategory", $this);
		$this->addRule("category", "Seleccione una categoría", "verifyCategory");
		$this->addElement("submit", "btnSubmit", "Insertar");
		
	}

	public function verifyFutureDate($value) {
		if ($value['F'] == '0' and $value['d'] == '0' and $value['Y'] == '0') {
			return TRUE;
		}
		return strtotime($value['Y'] . '/' . $value['F'] . '/' . $value['d']) > time();
	}
	
	public function verifyValidDate($value) {
		if ($value['F'] == '0' and $value['d'] == '0' and $value['Y'] == '0') {
			return true;
		}
		return checkdate($value['F'], $value['d'], $value['Y']);
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