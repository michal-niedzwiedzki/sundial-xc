<?php

class TransformationMock implements PersistenceTransformation {

	public static function freeze($value, array $params, stdClass $object) {
		return $value === "fourty-two" ? 42 : 0;
	}

	public static function revive($value, array $params, array $row) {
		return $value === 42 ? "fourty-two" : $value;
	}

}