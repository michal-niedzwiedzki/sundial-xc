<?php

final class ListingController extends Controller {

	/**
	 * @Title "Crear nuevo servicio"
	 */
	public function create() {
		$user = cMember::getCurrent();

		$type = HTTPHelper::rq("type");
		$adminMode = HTTPHelper::rq("mode") === "admin";
		$memberId = $adminMode ? HTTPHelper::rq("member_id") : $user->member_id;

		$this->view->isOffered = $type == "Offer";

		if (LIVE and $user->member_id == "ADMIN") {
			PageView::getInstance()->displayError("Lo siento, no se puede crear servicios nuevos con la cuenta del administrador.\nEs una cuenta especial para la administración de la aplicación.");
			return;
		}

		$form = new ListingCreateForm($type, $memberId, $adminMode);
		$this->view->form = $form;

		if (!$form->validate()) {
			return;
		}
		$form->process();
		$values = $form->exportValues();

		if ($adminMode) {
			$user->MustBeLevel(1);
			$member = new cMember;
			$member->LoadMember($memberId);
		} else {
			$member = $user;
		}

		// save listing
		$listing = new cListing($member, $values);
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
		$user = cMember::getCurrent();

		$title = HTTPHelper::rq("title");
		$type = HTTPHelper::rq("type");
		$adminMode = HTTPHelper::rq("mode") === "admin";
		$memberId = $adminMode ? HTTPHelper::rq("member_id") : $user->member_id;

		$this->view->isOffered = $type == "Offer";

		if (LIVE and $user->member_id == "ADMIN") {
			PageView::getInstance()->displayError("Lo siento, no se puede crear servicios nuevos con la cuenta del administrador.\nEs una cuenta especial para la administración de la aplicación.");
			return;
		}

		$listing = new cListing();
		$listing->LoadListing($title, $memberId, $type);

		$form = new ListingEditForm($listing, $adminMode);
		$this->view->form = $form;

		if (!$form->validate()) {
			return;
		}
		$form->freeze();
		$form->process();
		$values = $form->exportValues();

		$category = new cCategory();
		$category->LoadCategory($listing->category->id);

		$listing->category = $category;
		$listing->description = $values["description"];

		if ($listing->SaveListing()) {
			PageView::getInstance()->setMessage("El servicio ha sido guardado.");
		} else {
			PageView::getInstance()->displayError("Ha ocurrido un error en el momento de guardar los cambios. Intentalo otra vez mas tarde.");
		}
	}

	public function delete() {
		$type = HTTPHelper::rq("type");
		$mode = HTTPHelper::rq("mode");
		$memberId = HTTPHelper::rq("member_id");

		$user = cMember::getCurrent();

		$master = PageView::getInstance();
		($type == "Offer")
			? $title = 'Borrar servicios ofrecidos'
			: $title = 'Borrar servicios pedidos';

		$member = new cMember;
		if ($mode == "admin") {
			$user->MustBeLevel(1);
			$member->LoadMember($memberId);
		} else {
			$member = $user;
		}

		$itemsList = new cTitleList($type);
		$items = $itemsList->MakeTitleArray($member->member_id);
		$form = new ListingDeleteChecklist($items, $type, $mode, $memberId);
		$isEmpty = 0 === $itemsList->getCount($member->member_id);

		$this->view->title = $title;
		$this->view->form = $form;
		$this->view->isEmpty = $isEmpty;
		$master->title = $title;

		if ($isEmpty) {
			$mode == "self"
				? $this->view->errorMsg = "No tiene listado de servicios"
				: $this->view->errorMsg = $member->PrimaryName() . " no tiene listado de servicios.";
		}

		if (!$form->validate()) {
			return;
		}

		$form->freeze();
		$form->process();
		$values = $form->exportValues();

 		$deleted = 0;
		$listing = new cListing;
		foreach ($values as $key => $title) {
			$affected = 0;
			if (is_numeric($key)) { // Two of the values are hidden fields.  Need to skip those.
				$affected = $listing->DeleteListing($items[$key],$member->member_id,substr($_REQUEST['type'], 0, 1));
				$deleted += $affected;
			}
		}

		$master = PageView::getInstance();
		if ($deleted == 1) {
			$master->setMessage("1 servicio borrado.");
		} elseif ($deleted > 1) {
			$master->setMessage("{$deleted} servicios borrados.");
		} else {
			$master->displayError("Ha ocurrido un error borrando los datos. ¿Ha seleccionado elementos?");
		}
	}

	/**
	 * @Title "Servicio sin titulo"
	 */
	public function detail() {
		$title = HTTPHelper::get("title");
		$listing = new cListing();
		$listing->LoadListing($title, HTTPHelper::get("member_id"), substr(HTTPHelper::get("type"), 0, 1));
		$this->view->description = $listing->description;
		$this->view->rate = $listing->rate;
		$this->view->member = $listing->member;
		$this->view->title = $title;
		$title and PageView::getInstance()->title = $title;
	}

	/**
	 * @Title "Servicios para editar"
	 */
	public function to_edit() {
		if (HTTPHelper::rq("mode") == "admin") {
			cMember::getCurrent()->MustBeLevel(1);
			$member = new cMember();
			$member->LoadMember(HTTPHelper::rq("member_id"));
		} else {
			$member = cMember::getCurrent();
		}

		$listings = new cTitleList(HTTPHelper::get("type"));

		$this->view->count = $listings->getCount($member->member_id);
		$this->view->listing = $listings->DisplayMemberListings($member);
	}

}