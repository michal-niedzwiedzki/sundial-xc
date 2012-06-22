<?php

class Link {

	public static function to($controller, $action, $params) {
		static $base;
		$base or $base = Config::getInstance()->server->base;
		$url = "{$base}/{$controller}_{$action}.php?";
		foreach ($params as $param => $value) {
			$url .= urlencode($param) . "=" . urlencode($value) . "&";
		}
		return rtrim($url, "?&");
	}

}