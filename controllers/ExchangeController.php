<?php

final class ExchangeController extends Controller {

	/**
	 * @Title "Intercambios"
	 */
	public function menu() {
		$pending = new cTradesPending(HTTPHelper::session("user_login"));
		$this->view->numIn = $pending->numIn;
	}

}