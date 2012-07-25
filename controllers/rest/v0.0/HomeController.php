<?php

final class HomeController extends Controller {

	/**
	 * @Public
	 */
	public function index() {
		$this->page->status = "ok";
	}

	/**
	 * @Public
	 */
	public function get($id) {
		$this->page->id = $id;
	}

}