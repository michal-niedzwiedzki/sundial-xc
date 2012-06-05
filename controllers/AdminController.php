<?php

final class AdminController extends Controller {

	/**
	 * @Title "Menú de administración"
	 */
	public function menu() {
		$user = cMember::getCurrent();
		$user->MustBeLevel(1);

		$config = Config::getInstance();

		$tableName = DB::MEMBERS;
		$sql = "SELECT sum(balance) AS balance FROM $tableName";
		$balance = PDOHelper::fetchCell("balance", $sql, array());

		$this->page->cUser = $user;
		$this->page->config = $config;
		$this->page->balance = $balance;
	}

}