<?php

interface PersistenceTransformation {

	public static function freeze($value);

	public static function revive($value);

}