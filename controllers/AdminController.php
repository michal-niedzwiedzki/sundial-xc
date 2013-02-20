<?php

final class AdminController extends Controller {

	/**
	 * @Title "Menú de administración"
	 * @Level 1
	 */
	public function menu() {
		$config = Config::getInstance();

		$sql = "SELECT sum(balance) AS balance FROM member";
		$balance = PDOHelper::fetchCell("balance", $sql, array());

		$this->view->cUser = $user;
		$this->view->config = $config;
		$this->view->balance = $balance;
	}

}