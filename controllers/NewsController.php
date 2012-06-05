<?php

final class NewsController extends Controller {

	public function create() {
		include ROOT_DIR . "/legacy/news_create.php";
	}

	public function edit() {
		include ROOT_DIR . "/legacy/news_edit.php";
	}

	public function index() {
		include ROOT_DIR . "/legacy/news.php";
	}

	public function to_edit() {
		include ROOT_DIR . "/legacy/news_to_edit.php";
	}

}