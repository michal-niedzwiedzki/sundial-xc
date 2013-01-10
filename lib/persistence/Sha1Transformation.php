<?php

class Sha1Transformation implements PersistenceTransformation {

	public static function freeze($value) {
		return sha1($value);
	}

	public static function revive($value) {
		return NULL;
	}

}