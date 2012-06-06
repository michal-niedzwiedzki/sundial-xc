<?php

final class CategoryController extends Controller {

	/**
	 * @Title "Elegir una categoría"
	 */
	public function choose() {
		$categories = new cCategoryList;
		$categoryList = $categories->MakeCategoryArray();
		unset($categoryList[0]);

		$form = new CategoryChooseForm($categoryList);
		$this->page->form = $form;

		if (!$form->validate()) {
			return;
		}
		$form->freeze();
		$form->process();
		$values = $form->exportValues();

		// go to editing screen
		if (!isset($values["btnDelete"])) {
			header("Location: " . HTTP_BASE . "/category_edit.php?category_id=" . $values["category"]);
			return;
		}

		// delete category
		$category = new cCategory;
		$category->LoadCategory($values["category"]);
		if (!$category->HasListings()) {
			$category->DeleteCategory()
				? PageView::getInstance()->setMessage("La categoría ha sido borrada.")
				: PageView::getInstance()->setMessage("Error deleting category");
			return;
		}

		// cannot delete as listings defined
		$offered = new cListingGroup(OFFER_LISTING);
		$offered->LoadListingGroup(null, $values["category"]);
		$wanted = new cListingGroup(WANT_LISTING);
		$wanted->LoadListingGroup(null, $values["category"]);
		$this->page->offeredListings = $offered;
		$this->page->wantedListings = $wanted;
		$this->page->cannotDelete = TRUE;
	}

	/**
	 * @Title "Crear una nueva categoría de servicios"
	 * @Level 1
	 */
	public function create() {
		$form = new CategoryEditForm()
		$this->page->form = $form;
		if (!$form->validate()) {
			return;
		}
		$form->freeze();
		$form->process();
		$values = $form->exportValues();

		// save category
		$category = new cCategory($values["category"]);
		$category->SaveNewCategory()
			? PageView::getInstance()->setMessage("La nueva categoría ha sido creada.")
			: PageView::getInstance()->setMessage("No ha sido posible crear la nueva categoría. Intentalo otra vez mas tarde.");
		}

	/**
	 * @Title "Editar categoría"
	 * @Level 1
	 */
	public function edit() {
		$id = HTTPHelper::rq("category_id");
		$category = new cCategory();
		$category->LoadCategory($id);

		$form = new CategoryEditForm($id);
		$form->setDefaults(array("category" => $category->description));
		$this->page->form = $form;
		if (!$form->validate()) {
			return;
		}
		$form->freeze();
		$form->process();
		$values = $form->exportValues();

		// update category description
		$category->description = $values["category"];
		$category->SaveCategory()
			? PageView::getInstance()->setMessage("La categoría ha sido actualizada.")
			: PageView::getInstance()->setMessage("No ha sido posible guardar los cambios. Intentalo otra vez mas tarde.");
	}

}