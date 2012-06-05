<?php

final class HomeController extends Controller {

	/**
	 * @Public
	 */
	public function index() {
		$this->page->isLoggedOn = cMember::IsLoggedOn();
	}

}