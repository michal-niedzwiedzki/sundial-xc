<?php

final class HomeController extends Controller {

	/**
	 * @Public
	 * @Title "Posición global"
	 */
	public function index() {
		$this->page->isLoggedOn = cMember::IsLoggedOn();
	}

}