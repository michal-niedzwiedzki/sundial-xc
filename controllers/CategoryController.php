<?php

final class CategoryController extends Controller {

	/**
	 * @Title "Elegir una categoría"
	 */
	public function choose() {
		include ROOT_DIR . "/legacy/category_choose.php";
	}

	public function create() {
		include ROOT_DIR . "/legacy/category_create.php";
	}

	public function edit() {
		include ROOT_DIR . "/legacy/category_edit.php";
	}

}