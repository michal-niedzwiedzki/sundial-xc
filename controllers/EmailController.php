<?php

final class EmailController extends Controller {

	public function index() {
		include ROOT_DIR . "/legacy/email.php";
	}

}