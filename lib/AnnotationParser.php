<?php

/**
 * Annotation parser
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class AnnotationParser {

	public static function get(Reflector $reflection, $annotation) {
		// check if reflection provides doc comment
		if (!method_exists($reflection, "getDocComment")) {
			throw new Exception("Provided reflection object does not implement getDocComment() method");
		}
		// parse doc comment
		$lines = explode("\n", $reflection->getDocComment());
		foreach ($lines as $line) {
			// parse the first word
			$line = trim($line, "\t\n */");
			$pos = strpos($line, " ");
			$firstWord = ($pos === FALSE) ? $line : substr($line, 0, $pos);
			// check if word matches annotation
			if ($firstWord === "@" . $annotation) {
				// return TRUE if annotation has no parameter
				if ($pos === FALSE) {
					return TRUE;
				}
				// check if parameter is a valid json
				$out = json_decode(substr($line, $pos + 1));
				if ($out === NULL) {
					throw new Exception("Annotation '$line' could not be parsed");
				}
				return $out;
			}
		}
		return NULL;
	}

}