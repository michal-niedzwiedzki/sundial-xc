<?php

final class ListingEditForm extends Form {

	protected $userId;

	public function __construct(cListing $listing) {
		parent::__construct();
		$this->userId = $listing->getUserId();

		$this->addElement("hidden", "user_id", $this->userId);
		$this->addElement("hidden", "title", $listing->title);
		$this->addElement("hidden", "type", $listing->type);
		$categoryList = new cCategoryList();
		$this->addElement("select", "category", "Categoría", $categoryList->MakeCategoryArray());
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