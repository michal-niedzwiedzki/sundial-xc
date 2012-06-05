<?php

final class ExportController extends Controller {

	public function index() {
		include ROOT_DIR . "/legacy/export.php";
	}

}