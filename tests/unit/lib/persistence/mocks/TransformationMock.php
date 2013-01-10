<?php

class TransformationMock implements PersistenceTransformation {

	public static function freeze($value) {
		return $value === "fourty-two" ? 42 : 0;
	}

	public static function revive($value) {
		return $value === 42 ? "fourty-two" : $value;
	}

}