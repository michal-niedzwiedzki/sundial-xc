<?php

final class HolidayController extends Controller {

	/**
	 * @Title "Desactivar listado por vacaciones"
	 */
	public function index() {
		$currentUser = User::getCurrent();
		$userId = HTTPHelper::rq("user_id");
		$adminMode = HTTPHelper::rq("admin") === "admin";
		if ($adminMode) {
			Assert::true($currentUser->isAdmin());
			$user = User::getById($userId);
		} else {
			$user = $currentUser;
		}

		// prepare form and view
		$form = new HolidayForm($userId, $adminMode);
		$this->view->form = $form;
		$this->view->adminMode = $adminMode;

		// validate form
		if (!$form->validate()) {
			return;
		}
		$values= $form->process();
		$date = $values["return_date"];
		$returnDate = new cDateTime($date["Y"] . "/" . $date["F"] . "/" . $date["d"]);

		// deactivate offered listings
		$listings = new cListingGroup(OFFER_LISTING);
		$listings->LoadListingGroup(NULL, "%", $user->id);
		$listings->InactivateAll($returnDate);

		// deactivate wanted listings
		$listings = new cListingGroup(WANT_LISTING);
		$listings->LoadListingGroup(NULL, "%", $user->id);
		$listings->InactivateAll($returnDate);

		PageView::getInstance()->setMessage("El listado ha sido desactivado con exito.");
	}

}