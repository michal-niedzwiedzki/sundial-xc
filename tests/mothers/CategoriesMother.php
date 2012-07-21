<?php

final class CategoriesMother {

	public static function create($description, $parentId = NULL) {
		$row = array(
			"parent_id" => $parentId,
			"description" => $description
		);
		$id = PDOHelper::insert(DB::CATEGORIES, $row);
		$category = new cCategory();
		$category->LoadCategory($id);
		return $category;
	}

}