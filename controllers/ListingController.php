<?php

final class ListingController extends Controller {

	/**
	 * @Title "Crear nuevo servicio"
	 */
	public function create() {
		$currentUser = User::getCurrent();

		$type = HTTPHelper::rq("type");
		$adminMode = HTTPHelper::rq("mode") === "admin";
		$userId = $adminMode ? HTTPHelper::rq("user_id") : $currentUser->id;

		$this->view->isOffered = $type == "Offer";

		if (LIVE and $user->isAdmin()) {
			PageView::getInstance()->displayError("Lo siento, no se puede crear servicios nuevos con la cuenta del administrador.\nEs una cuenta especial para la administración de la aplicación.");
			return;
		}

		$form = new ListingCreateForm($type, $userId, $adminMode);
		$this->view->form = $form;

		if (!$form->validate()) {
			return;
		}
		$values = $form->process();

		if ($adminMode) {
			Assert::true($user->isAdmin());
			$user = User::getById($userId);
		} else {
			$user = $currentUser;
		}

		// save listing
		$listing = new cListing($user, $values);
		if ($listing->SaveNewListing()) {
			$this->view->saved = TRUE;
			PageView::getInstance()->setMessage("El nuevo servicio ha sido creado.");
		} else {
			PageView::getInstance()->displayError("Ha ocurrido un error en el momento de guardar los cambios. Intentalo otra vez mas tarde.");
		}
	}

	/**
	 * @Title "Editar servicio"
	 */
	public function edit() {
		$currentUser = User::getCurrent();

		$title = HTTPHelper::rq("title");
		$type = HTTPHelper::rq("type");
		$userId = $currentUser->id;

		$this->view->isOffered = $type == "Offer";

		if (LIVE and $user->isAdmin()) {
			PageView::getInstance()->displayError("Lo siento, no se puede crear servicios nuevos con la cuenta del administrador.\nEs una cuenta especial para la administración de la aplicación.");
			return;
		}

		$listing = new cListing();
		$listing->LoadListing($title, $userId, $type);

		$form = new ListingEditForm($listing);
		$this->view->form = $form;

		if (!$form->validate()) {
			return;
		}
		$values = $form->process();

		$category = new cCategory();
		$category->LoadCategory($listing->category->id);

		$listing->category = $category;
		$listing->description = $values["description"];

		$listing->SaveListing()
			? PageView::getInstance()->setMessage("El servicio ha sido guardado.")
			: PageView::getInstance()->displayError("Ha ocurrido un error en el momento de guardar los cambios. Intentalo otra vez mas tarde.");
	}

	public function delete() {
		$type = HTTPHelper::rq("type");
		$mode = HTTPHelper::rq("mode");
		$userId = HTTPHelper::rq("user_id");

		$currentUser = User::getCurrent();

		$master = PageView::getInstance();
		($type == "Offer")
			? $title = 'Borrar servicios ofrecidos'
			: $title = 'Borrar servicios pedidos';

		if ($mode === "admin") {
			Assert::true($user->isAdmin());
			$user = User::getById($userId);
		} else {
			$user = $currentUser;
		}

		$itemsList = new cTitleList($type);
		$items = $itemsList->MakeTitleArray($user->id);
		$form = new ListingDeleteChecklist($items, $type, $mode, $userId);
		$isEmpty = 0 === $itemsList->getCount($user->id);

		$this->view->title = $title;
		$this->view->form = $form;
		$this->view->isEmpty = $isEmpty;
		$master->title = $title;

		if ($isEmpty) {
			$mode == "self"
				? $this->view->errorMsg = "No tiene listado de servicios"
				: $this->view->errorMsg = $user->fullName . " no tiene listado de servicios.";
		}

		if (!$form->validate()) {
			return;
		}
		$values = $form->process();

 		$deleted = 0;
		$listing = new cListing;
		foreach ($values as $key => $title) {
			$affected = 0;
			if (is_numeric($key)) { // Two of the values are hidden fields.  Need to skip those.
				$affected = $listing->DeleteListing($items[$key], $user->_id, substr($_REQUEST['type'], 0, 1));
				$deleted += $affected;
			}
		}

		if ($deleted == 1) {
			PageView::getInstance()->setMessage("1 servicio borrado.");
		} elseif ($deleted > 1) {
			PageView::getInstance()->setMessage("{$deleted} servicios borrados.");
		} else {
			PageView::getInstance()->displayError("Ha ocurrido un error borrando los datos. ¿Ha seleccionado elementos?");
		}
	}

	/**
	 * @Title "Servicio sin titulo"
	 */
	public function detail() {
		$title = HTTPHelper::get("title");
		$listing = new cListing();
		$listing->LoadListing($title, HTTPHelper::get("user_id"), substr(HTTPHelper::get("type"), 0, 1));
		$this->view->description = $listing->description;
		$this->view->rate = $listing->rate;
		$this->view->user = $listing->getUser();
		$this->view->title = $title;
		$title and PageView::getInstance()->title = $title;
	}

	/**
	 * @Title "Servicios para editar"
	 */
	public function to_edit() {
		if (HTTPHelper::rq("mode") == "admin") {
			Assert::true(User::getCurrent()->isAdmin());
			$user = User::getById(HTTPHelper::rq("user_id"));
		} else {
			$user = User::getCurrent();
		}

		$listings = new cTitleList(HTTPHelper::get("type"));

		$this->view->count = $listings->getCount($user->id);
		$this->view->listing = $listings->DisplayMemberListings($user);
	}

}