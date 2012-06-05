<?php

final class InfoController extends Controller {

	public function permissions() {
		include ROOT_DIR . "/legacy/info_permissions.php";
	}

	public function url() {
		include ROOT_DIR . "/legacy/info_url.php";
	}

}