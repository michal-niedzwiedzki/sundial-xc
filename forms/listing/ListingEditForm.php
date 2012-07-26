<?php

final class ListingEditForm extends Form {

	protected $memberId;
	
	public function __construct(cListing $listing, $adminMode = FALSE) {
		parent::__construct();
		$this->memberId = $listing->member->getId();
		
		if ($adminMode) {
			$this->addElement("hidden", "mode", "admin");
			if ($memberId) {
				$this->addElement("hidden", "member_id", $this->memberId);
			} else {
				$ids = new cMemberGroup;
				$ids->LoadMemberGroup();
				$this->addElement("select", "member_id", "ID de Socio", $ids->MakeIDArray());
			}
		} else {
			$this->addElement("hidden", "member_id", $this->memberId);
			$this->addElement("hidden", "mode", "self");
		}

		$this->addElement("hidden", "title", $listing->title);
		$this->addElement("hidden", "type", $listing->type);
		$categoryList = new cCategoryList();
		$this->addElement("select", "category", "Categoría", $categoryList->MakeCategoryArray());
#		USE_RATES
#			? $this->addElement("text", "rate", "Rate", array("size" => 15, "maxlength" => 30))
#			: $this->addElement("hidden", "rate");
		$this->addElement("textarea", "description", "Descripción", array("cols" => 45, "rows" => 5, "wrap" => "soft"));
#		$this->registerRule("verifyCategory", "function", "verifyCategory", $this);
#		$this->addRule("category", "Seleccione una categoría", "verifyCategory");
		$this->addElement("submit", "btnSubmit", "Guardar");
		
		$this->setDefaults(array(
			"title" => $listing->title,
			"category" => $listing->category->getId(),
			"description" => $listing->description,
		));
	}

	public function verifyCategory($value) {
		return $value != "0";
	}

}