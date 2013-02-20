<?php

interface PersistenceTransformation {

	public static function freeze($value, array $params, stdClass $object);

	public static function revive($value, array $params, array $row);

}