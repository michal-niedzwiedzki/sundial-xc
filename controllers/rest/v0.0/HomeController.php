<?php

final class HomeController extends Controller {

	/**
	 * @Public
	 */
	public function index() {
		$this->view->status = "ok";
	}

	/**
	 * @Public
	 */
	public function get($id) {
		$this->view->id = $id;
	}

}