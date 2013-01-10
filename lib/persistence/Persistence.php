<?php

final class Persistence {

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

			// ignore primary key updates
			if ($keyName === $name) {
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
				if (!class_exists($transformation)) {
					throw new Exception("Transformation class '$transformation' not found");
				}
				$value = $transformation::freeze($value);
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

	public static function revive($row, stdClass $subject) {
		$rc = new ReflectionClass($subject);
		foreach ($rc->getProperties() as $rp) {

			// check if column is supposed to be persistent and exists in row
			$column = AnnotationParser::get($rp, "Column");
			if (!$column or !isset($row[$column])) {
				continue;
			}
			$property = $rp->getName();

			// run through transformations
			$transformations = AnnotationParser::getAll($rp, "Transformation");
			foreach ($transformations as $transformation) {
				$value = $transformation::freeze($value);
			}

			// load it up
			$subject->$property = $row[$column];
		}
	}

}