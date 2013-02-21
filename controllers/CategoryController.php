<?php

final class CategoryController extends Controller {

	/**
	 * @Title "Elegir una categoría"
	 * @Admin
	 */
	public function choose() {
		$categories = Category::getAll();
		$list = array_map(function(Category $c) { return $c->description; }, $categories);

		$form = new CategoryChooseForm($list);
		$this->view->form = $form;

		if (!$form->validate()) {
			return;
		}
		$values = $form->process();
		$categoryId = $values["category"];

		// go to editing screen
		if (!isset($values["btnDelete"])) {
			header("Location: " . HTTP_BASE . "/category_edit.php?category_id=" . $values["category"]);
			return;
		}

		// delete category
		$category = Category::getById($categoryId);
		$offers = $category->getOffers();
		if (!empty($offers)) {
			$category->delete()
				? PageView::getInstance()->setMessage("La categoría ha sido borrada.")
				: PageView::getInstance()->setMessage("Error deleting category");
			return;
		}

		// cannot delete as listings defined
		$this->view->offers = $offered;
		$this->view->cannotDelete = TRUE;
	}

	/**
	 * @Title "Crear una nueva categoría de servicios"
	 * @Admin
	 */
	public function create() {
		$form = new CategoryEditForm();
		$this->view->form = $form;
		if (!$form->validate()) {
			return;
		}
		$values = $form->process();

		// save category
		$category = new Category();
		$category->description = $values["category"];
		$category->save()
			? PageView::getInstance()->setMessage("La nueva categoría ha sido creada.")
			: PageView::getInstance()->setMessage("No ha sido posible crear la nueva categoría. Intentalo otra vez mas tarde.");
		}

	/**
	 * @Title "Editar categoría"
	 * @Admin
	 */
	public function edit() {
		$id = HTTPHelper::rq("category_id");
		$category = Category::getById($id);

		$form = new CategoryEditForm($id);
		$form->setDefaults(array("category" => $category->description));
		$this->view->form = $form;
		if (!$form->validate()) {
			return;
		}
		$values = $form->process();

		// update category description
		$category->description = $values["category"];
		$category->save()
			? PageView::getInstance()->setMessage("La categoría ha sido actualizada.")
			: PageView::getInstance()->setMessage("No ha sido posible guardar los cambios. Intentalo otra vez mas tarde.");
	}

}