<?php

final class AdminController extends Controller {

	/**
	 * @Title "Menú de administración"
	 */
	public function menu() {
		$user = cMember::getCurrent();
		$user->MustBeLevel(1);

		$config = Config::getInstance();

		$sql = "SELECT sum(balance) AS balance FROM member";
		$balance = PDOHelper::fetchCell("balance", $sql, array());

		$this->page->cUser = $user;
		$this->page->config = $config;
		$this->page->balance = $balance;
	}

}