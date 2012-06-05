<?php

class cCategory {

	public $id;
	public $parent;
	public $description;

	public function __construct($description = NULL, $parent = NULL) {
		if ($description) {
			$this->description = $description;
			$this->parent = $parent;
		}
	}

	public function SaveNewCategory() {
		$this->id = PDOHelper::insert(DB::CATEGORIES, array("parent_id" => $this->parent, "description" => $this->description));
		return (boolean)$this->id;
	}

	public function SaveCategory() {
		return PDOHelper::update(DB::CATEGORIES, array("parent_id" => $this->parent, "description" => $this->description), "category_id = :id", array("id" => (int)$this->id));
	}

	public function LoadCategory($id) {
		$tableName = DB::CATEGORIES;
		$sql = "SELECT parent_id, description FROM $tableName WHERE category_id = :id";
		try {
			$row = PDOHelper::fetchRow($sql, array("id" => $id));
		} catch (Exception $e) {
			cError::getInstance()->Error("No ha sido posible encontrar el código para la categoría '".$id."'.  Intentalo otra vez mas tarde.");
			include "redirect.php";
			return FALSE;
		}
		$this->id = $id;
		$this->parent = $row['parent_id'];
		$this->description = $row['description'];
		return TRUE;
	}

	public function DeleteCategory() {
		$out = PDOHelper::delete(DB::CATEGORIES, "category_id = :id", array("id" => $id));
		if (!$out) {
			cError::getInstance()->Error("Ha ocurrido un error borrando el código de la categoría '".$id."'.  Intentalo otra vez mas tarde.");
			include "redirect.php";
			return FALSE;
		}
		unset($this);
		return TRUE;
	}

	public function ShowCategory() {
		return $this->id .", ". $this->description . "<BR>";
	}

	public function HasListings() {
		$listings = new cListingGroup(OFFER_LISTING);
		if ($listings->LoadListingGroup(null, $this->id)) {
			return TRUE;
		}
		$listings = new cListingGroup(WANT_LISTING);
		if ($listings->LoadListingGroup(null, $this->id)) {
			return TRUE;
		}
		return FALSE;
	}

}