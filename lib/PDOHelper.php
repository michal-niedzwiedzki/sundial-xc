<?php

/**
 * Helper class for interfacing with PDO
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class PDOHelper {

	protected static $lastQuery = "";

	/**
	 * Constrcutor
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	private function __construct() { }

	public static function getLastExecutedQuery() {
		return self::$lastQuery;
	}

	/**
	 * Fetch and return single cell from database
	 *
	 * @param string $column
	 * @param string $sql
	 * @param array $params
	 * @return scalar
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function fetchCell($column, $sql, array $params = array()) {
		// prepare statement and bind parameters
		$stmt = DB::getPDO()->prepare($sql);
		foreach ($params as $param => $value) {
			$stmt->bindValue($param, $value);
		}

		// make call and extract cell
		self::$lastQuery = $sql;
		Assert::true($stmt->execute());
		$out = $stmt->fetch();
		Assert::hasKey($column, $out);
		return $out[$column];
	}

	/**
	 * Fetch and return single row from database
	 *
	 * @param string $sql
	 * @param array $params
	 * @return scalar[]
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function fetchRow($sql, array $params = array()) {
		// prepare statement and bind parameters
		$stmt = DB::getPDO()->prepare($sql);
		foreach ($params as $param => $value) {
			$stmt->bindValue($param, $value);
		}

		// make call and fetch fow
		self::$lastQuery = $sql;
		Assert::true($stmt->execute());
		return $stmt->fetch();
	}

	/**
	 * Fetch and return rows from database
	 *
	 * @param string $sql
	 * @param array $params
	 * @return scalar[][]
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function fetchAll($sql, array $params = array()) {
		// prepare statement and bind parameters
		$stmt = DB::getPDO()->prepare($sql);
		foreach ($params as $param => $value) {
			$stmt->bindValue($param, $value);
		}

		// make call and fetch fow
		self::$lastQuery = $sql;
		Assert::true($stmt->execute());
		return $stmt->fetchAll();
	}

	/**
	 * Insert into database and return primary key
	 *
	 * @param string $tableName
	 * @param array $params
	 * @return scalar primary key of last inserted row
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function insert($tableName, array $params) {
		// prepare placeholders
		$columns = array_keys($params);
		$placeholders = array();
		foreach ($params as $column => $value) {
			$placeholders[] = ":$column";
		}

		// prepare stamement
		$c = implode(", ", $columns);
		$p = implode(", ", $placeholders);
		$sql = "INSERT INTO $tableName ($c) VALUES ($p)";
		$pdo = DB::getPDO();
		$stmt = $pdo->prepare($sql);

		// bind parameters
		foreach ($params as $column => $value) {
			$stmt->bindValue($column, $value);
		}

		// insert and return primary key (if exist) or TRUE
		self::$lastQuery = $sql;
		Assert::true($stmt->execute()); 
		$id = $pdo->lastInsertId();
		return $id ? $id : TRUE;
	}

	/**
	 * Update database table and return affected rows count
	 *
	 * @param string $tableName
	 * @param array $params
	 * @param string $where
	 * @param array $whereParams
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function update($tableName, array $params, $where, array $whereParams = array()) {
		// prepare updates
		$updates = array();
		foreach ($params as $column => $value) {
			$updates[] = "$column = :$column";
		}
		$updatesList = implode(", ", $updates);

		// prepare stamement
		$sql = "UPDATE $tableName SET $updatesList WHERE $where";
		$stmt = DB::getPDO()->prepare($sql);

		// bind parameters
		foreach ($params as $column => $value) {
			$stmt->bindValue($column, $value);
		}
		foreach ($whereParams as $column => $value) {
			$stmt->bindValue($column, $value);
		}

		// update and return affected rows count
		self::$lastQuery = $sql;
		Assert::true($stmt->execute());
		return $stmt->rowCount();
	}

	/**
	 * Delete from database table and return affected rows count
	 *
	 * @param string $tableName
	 * @param string $where
	 * @param array $whereParams
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function delete($tableName, $where, array $whereParams = array()) {
		// prepare stamement
		$sql = "DELETE FROM $tableName WHERE $where";
		$stmt = DB::getPDO()->prepare($sql);

		// bind parameters
		foreach ($whereParams as $column => $value) {
			$stmt->bindValue($column, $value);
		}

		// delete and return affected rows count
		self::$lastQuery = $sql;
		Assert::true($stmt->execute());
		return $stmt->rowCount();
	}

	/**
	 * Set engine property
	 *
	 * @param string $property
	 * @param string $value
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function set($property, $value) {
		return DB::getPDO()->exec("SET $property = $value");
	}

}