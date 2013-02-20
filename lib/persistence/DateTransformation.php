<?php

/**
 * Transformation for date conversion between Unix and SQL timestamp format
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class DateTransformation implements PersistenceTransformation {

	public static function freeze($value, array $params, stdClass $object) {
		if (is_null($value)) {
			return NULL;
		}
		if (is_int($value)) {
			return date("Y-m-d H:i:s", $value);
		}
		return $value;
	}

	public static function revive($value, array $params, array $row) {
		return $value ? strtotime($value) : NULL;
	}

}