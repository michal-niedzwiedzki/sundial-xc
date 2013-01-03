<?php

class Link {

	public static function to($controller, $action = NULL, $params = NULL) {
		static $base;
		$base or $base = Config::getInstance()->server->base;
		$url = $action ? "{$base}/{$controller}_{$action}.php?" : "{$base}/{$controller}.php";
		foreach ($params as $param => $value) {
			$url .= urlencode($param) . "=" . urlencode($value) . "&";
		}
		return rtrim($url, "?&");
	}

}