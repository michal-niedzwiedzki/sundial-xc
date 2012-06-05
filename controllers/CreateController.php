<?php

final class CreateController extends Controller {

	public function db() {
		include ROOT_DIR . "/legacy/create_db.php";
	}

	public function info() {
		include ROOT_DIR . "/legacy/create_info.php";
	}

}