<?php

final class EditController extends Controller {

	public function balance() {
		include ROOT_DIR . "/legacy/edit_balance.php";
	}

	public function info() {
		include ROOT_DIR . "/legacy/edit_info.php";
	}

}