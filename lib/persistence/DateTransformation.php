<?php

class DateTransformation implements PersistenceTransformation {

	public static function freeze($value) {
		if (is_null($value)) {
			return NULL;
		}
		if (is_int($value)) {
			return date("Y-m-d H:i:s", $value);
		}
		return $value;
	}

	public static function revive($value) {
		return $value ? strtotime($value) : NULL;
	}

}