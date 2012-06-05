<?php

class cInfo {

	public function LoadOne($id) {
		$tableName = DB::PAGES;
		$sql = "SELECT * FROM $tableName WHERE id = :id LIMIT 0, 1";
		$row = PDOHelper::fetchRow($sql, array("id" => $id));
		return empty($row) ? FALSE : $row;
	}

	public function LoadPages() {
		$tableName = DB::PAGES;
		$sql = "SELECT * FROM $tableName";
		$out = PDOHelper::fetchAll($sql, array());
		$pgs = array();
		foreach ($out as $row) {
			$pgs[] = $row;
		}
		return $pgs;
	}

}