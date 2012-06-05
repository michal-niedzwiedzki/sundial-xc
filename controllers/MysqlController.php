<?php

final class MysqlController extends Controller {

	public function backup() {
		include ROOT_DIR . "/legacy/mysql_backup.php";
	}

}