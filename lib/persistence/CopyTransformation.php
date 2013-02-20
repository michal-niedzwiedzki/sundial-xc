<?php

/**
 * Transformation for copying value into object's property
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class CopyTransformation implements PersistenceTransformation {

	public static function freeze($value, array $params, stdClass $object) {
		return $value;
	}

	public static function revive($value, array $params, array $row) {
		if (empty($params)) {
			throw new Exception("Transformation parameter required: property, i.e. @CopyTransformation \"CopyTransformation\", \"backup\"");
		}
		$property = reset($params);
		$object->$property = $value;
	}

}