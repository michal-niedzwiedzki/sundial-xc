<?php

final class Persistence {

	/**
	 * Store object into database and return its primary key value, otherwise FALSE
	 *
	 * @param stdClass $subject
	 * @return mixed
	 */
	public static function freeze(stdClass $subject) {
		$rc = new ReflectionClass($subject);
		$tableName = AnnotationParser::get($rc, "TableName");
		$keyName = AnnotationParser::get($rc, "PrimaryKey");
		$keyValue = NULL;

		foreach ($rc->getProperties() as $rp) {

			// check if column is supposed to be persistent
			$column = AnnotationParser::get($rp, "Column");
			if (!$column) {
				continue;
			}
			$name = $rp->getName();
			$value = $rp->getValue($subject);
			$notNull = AnnotationParser::get($rp, "NotNull");

			// ignore primary key updates but keep its value
			if ($keyName === $column) {
				$keyValue = $value;
				continue;
			}

			// ignore when null on @NotNull column
			if ($notNull === TRUE and $value === NULL) {
				continue;
			}

			// run through transformations and store for insert/update
			$transformations = AnnotationParser::getAll($rp, "Transformation");
			foreach ($transformations as $transformation) {
				$value = self::transform(TRUE, $transformation, $value, $subject);
			}
			$a[$column] = $value;
		}

		// insert or update
		if ($keyValue === NULL) {
			$ok = PDOHelper::insert($tableName, $a);
			$ok and $keyValue = $ok;
		} else {
			$ok = PDOHelper::update($tableName, $a, "{$keyName} = :{$keyName}", array($keyName => $keyValue));
		}
		return $ok ? $keyValue : FALSE;
	}

	/**
	 * Hydrate object with database row
	 *
	 * @param array $row
	 * @param stdClass $subject
	 */
	public static function revive(array $row, stdClass $subject) {
		$rc = new ReflectionClass($subject);
		foreach ($rc->getProperties() as $rp) {

			// check if column is supposed to be persistent and exists in row
			$column = AnnotationParser::get($rp, "Column");
			if (!$column or !isset($row[$column])) {
				continue;
			}
			$property = $rp->getName();
			$value = $row[$column];

			// run through transformations
			$transformations = AnnotationParser::getAll($rp, "Transformation");
			foreach ($transformations as $transformation) {
				$value = self::transform(FALSE, $transformation, $value, $row);
			}

			// load it up
			$subject->$property = $value;
		}
	}

	/**
	 * Apply transformation to a value within given context
	 *
	 * When transformation is array first element is used as transformation name,
	 * other elements are passed as additional parameters to operation.
	 * Context can be object (for freeze) or array (for revive).
	 *
	 * @param boolean $freeze TRUE for freeze, FALSE for revive
	 * @param string|array $transformation to be applied, with additional parameters when passed as array
	 * @param object|array $context for transformation
	 * @return mixed
	 * @throws Exception
	 */
	public static function transform($freeze, $transformation, $value, $context) {
		if (is_array($transformation)) {
			$params = $transformation;
			$transformation = array_shift($params);
		} else {
			$params = array();
		}
		if (!class_exists($transformation)) {
			throw new Exception("Transformation class '$transformation' not found");
		}
		$op = $freeze ? "freeze" : "revive";
		return $transformation::freeze($value, $params, $row);
	}

}