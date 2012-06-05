<?php

final class TakeController extends Controller {

	public function monthly_fee() {
		include ROOT_DIR . "/legacy/take_monthly_fee.php";
	}

}