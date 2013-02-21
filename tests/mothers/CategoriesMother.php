<?php

final class CategoriesMother {

	public static function create($description, $parentId = NULL) {
		$category = new Category();
		$category->parentId = $parentId;
		$category->description = $description;
		$category->save();
		return $category;
	}

}