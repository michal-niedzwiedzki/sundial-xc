<?php

/**
 * Helper class for interfacing with PDO
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class PDOHelper {

	protected static $lastQuery = "";
	protected static $lastParams = array();

	/**
	 * Constrcutor
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	private function __construct() { }

	public static function getLastExecutedQuery($withParams = FALSE) {
		if (!$withParams) {
			return self::$lastQuery;
		}
		$columns = array();
		$values = array();
		foreach (self::$lastParams as $column => $value) {
			$columns[] = ":{$column}";
			$values[] = is_numeric($value) ? $value : "'{$value}'";
		}
		return str_replace($columns, $values, self::$lastQuery);
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
		self::$lastParams = $params;
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
		self::$lastParams = $params;
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
		self::$lastParams = $params;
		Assert::true($stmt->execute());
		return $stmt->fetchAll();
	}

	/**
	 * Insert into database and return primary key or FALSE
	 *
	 * @param string $tableName
	 * @param array $params
	 * @return mixed|FALSE
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function insert($tableName, array $params) {
		// prepare placeholders
		$columns = array_keys($params);
		$placeholders = array();
		foreach ($params as $column => $value) {
			$cols[] = "`$column`";
			$placeholders[] = ":$column";
		}

		// prepare stamement
		$c = implode(", ", $cols);
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
		self::$lastParams = $params;
		return $stmt->execute() ? $pdo->lastInsertId() : FALSE;
	}

	/**
	 * Update database table and return affected rows count or FALSE
	 *
	 * @param string $tableName
	 * @param array $params
	 * @param string $where
	 * @param array $whereParams
	 * @return int|FALSE
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function update($tableName, array $params, $where, array $whereParams = array()) {
		// prepare updates
		$updates = array();
		foreach ($params as $column => $value) {
			$updates[] = "`$column` = :$column";
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
		self::$lastParams = array_merge($params, $whereParams);
		return $stmt->execute() ? $stmt->rowCount() : FALSE;
	}

	/**
	 * Delete from database table and return affected rows count or FALSE
	 *
	 * @param string $tableName
	 * @param string $where
	 * @param array $whereParams
	 * @return int|FALSE
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
		self::$lastParams = array_merge($params, $whereParams);
		return $stmt->execute() ? $stmt->rowCount() : FALSE;
	}

	public static function begin() {
		DB::getPDO()->beginTransaction();
	}

	public static function commit() {
		DB::getPDO()->commit();
	}

	public static function rollBack() {
		DB::getPDO()->rollBack();
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