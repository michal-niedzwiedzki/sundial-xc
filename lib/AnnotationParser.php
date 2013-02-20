<?php

/**
 * Annotation parser
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class AnnotationParser {

	private static $cache = array();

	public static function get(Reflector $reflection, $annotation) {
		$key = crc32($reflection->__toString());
		$annotation[0] === "@" or $annotation = "@" . $annotation;
		isset(self::$cache[$key]) or self::$cache[$key] = self::parse($reflection);
		return isset(self::$cache[$key][$annotation])
			? self::$cache[$key][$annotation][0]
			: FALSE;
	}

	public static function getAll(Reflector $reflection, $annotation) {
		$key = crc32($reflection->__toString());
		$annotation[0] === "@" or $annotation = "@" . $annotation;
		isset(self::$cache[$key]) or self::$cache[$key] = self::parse($reflection);
		return isset(self::$cache[$key][$annotation])
			? self::$cache[$key][$annotation]
			: array();
	}

	public static function parse(Reflector $reflection) {
		// check if reflection provides doc comment
		if (!method_exists($reflection, "getDocComment")) {
			throw new Exception("Reflector of class " . get_class($reflection) . " does not implement getDocComment() method");
		}

		// parse doc comment
		$out = array();
		$lines = explode("\n", $reflection->getDocComment());
		foreach ($lines as $line) {


			// check if line starts with @
			$line = trim($line, "\t\n */");
			if ($line[0] !== "@") {
				continue;
			}

			// get annotation value
			$pos = strpos($line, " ");
			if ($pos === FALSE) {
				$firstWord = $line;
				$value = TRUE;
			} else {
				$firstWord = substr($line, 0, $pos);
				$rest = trim(substr($line, $pos + 1));
				if ($rest === "null" or $rest === "NULL" or $rest === "Null") {
					$value = NULL;
				} else {
					$value = json_decode($rest); // try to decode JSON string
					NULL === $value and $value = json_decode("[{$rest}]", TRUE); // try to decode JSON string as hash array
					NULL === $value and $value = $rest; // fall back to raw string
				}
			}

			// set the value
			isset($out[$firstWord]) or $out[$firstWord] = array();
			$out[$firstWord][] = $value;
		}
		return $out;
	}

}