<?php

/**
 * Transformation for boolean values into integers
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class BooleanTransformation implements PersistenceTransformation {

	public static function freeze($value, array $params, stdClass $object) {
		return (int)$value;
	}

	public static function revive($value, array $params, array $row) {
		return (boolean)$value;
	}

}