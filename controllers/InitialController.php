<?php

final class InitialController extends Controller {

	public function mailing() {
		include ROOT_DIR . "/legacy/initial_mailing.php";
	}

}