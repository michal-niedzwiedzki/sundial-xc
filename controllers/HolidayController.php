<?php

final class HolidayController extends Controller {

	/**
	 * @Title "Desactivar listado por vacaciones"
	 */
	public function index() {
		$user = cMember::getCurrent();
		$memberId = HTTPHelper::rq("mode");
		$adminMode = HTTPHelper::rq("admin") === "admin";
		if ($adminMode) {
			$user->MustBeLevel(1);
			$member = new cMember();
			$member->LoadMember($memberId);
		} else {
			$member = $user;
		}

		// prepare form and view
		$form = new HolidayForm($memberId, $adminMode);
		$this->page->form = $form;
		$this->page->adminMode = $adminMode;
		
		// validate form
		if (!$form->validate()) {
			return;
		}
		$form->freeze();
		$form->process();
		$values = $form->exportValues();
		$date = $values["return_date"];
		$returnDate = new cDateTime($date["Y"] . "/" . $date["F"] . "/" . $date["d"]);
		
		// deactivate offered listings
		$listings = new cListingGroup(OFFER_LISTING);
		$listings->LoadListingGroup(NULL, "%", $member->member_id);
		$listings->InactivateAll($returnDate);

		// deactivate wanted listings
		$listings = new cListingGroup(WANT_LISTING);
		$listings->LoadListingGroup(NULL, "%", $member->member_id);
		$listings->InactivateAll($returnDate);
		
		PageView::getInstance()->setMessage("El listado ha sido desactivado con exito.");
	}

}