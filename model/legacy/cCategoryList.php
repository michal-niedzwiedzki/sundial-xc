<?php

class cCategoryList {

	/**
	 * @var cCategory[]
	 */
	public $category = array();

	public function LoadCategoryList($activeOnly = FALSE, $type = "%") {
		$categoriesTable = DB::CATEGORIES;
		$listingsTable = DB::LISTINGS;
		if ($activeOnly) {
			$sql = "
				SELECT DISTINCT c.category_id, c.description
				FROM $categoriesTable AS c, $listingsTable AS l
				WHERE l.category_code = c.category_id
					AND l.status = 'A'
					AND l.type LIKE :type
				ORDER BY c.description
			";
			$rows = PDOHelper::fetchAll($sql, array("type" => $type));
		} else {
			$sql = "SELECT category_id, description FROM $categoriesTable ORDER BY description";
			$rows = PDOHelper::fetchAll($sql);
		}

		foreach ($rows as $i => $row) {
			$this->category[$i] = new cCategory();
			$this->category[$i]->LoadCategory($row["category_id"]);
		}

		if (empty($rows)) {
			cError::getInstance()->Error("No ha sido posible encontrar el registro de una categoría.  Intentalo otra vez mas tarde.");
			return FALSE;
		}
		return TRUE;
	}

	public function MakeCategoryArray($activeOnly = FALSE, $type = "%") {
		$array[0] = "";
		if ($this->LoadCategoryList($activeOnly, $type)) {
			foreach ($this->category as $category) {
				$array[$category->id] = $category->description;
			}
		}
		return $array;
	}

}