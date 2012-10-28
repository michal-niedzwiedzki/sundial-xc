<?php

abstract class Controller {

	protected $view;

	public function getView() {
		return $view;
	}

	public function setView(View $view) {
		$this->view = $view;
	}

}