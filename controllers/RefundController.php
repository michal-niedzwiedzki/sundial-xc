<?php

final class RefundController extends Controller {

	public function monthly_fee() {
		include ROOT_DIR . "/legacy/refund_monthly_fee.php";
	}

	public function service_charge() {
		include ROOT_DIR . "/legacy/refund_service_charge.php";
	}

}