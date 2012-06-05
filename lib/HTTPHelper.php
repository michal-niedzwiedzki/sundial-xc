<?php

final class HTTPHelper {

	public static function method() {
		return $_SERVER["REQUEST_METHOD"];
	}

	public static function get($parameter, $default = NULL) {
		return isset($_GET[$parameter]) ? $_GET[$parameter] : $default;
	}

	public static function post($parameter, $default = NULL) {
		return isset($_POST[$parameter]) ? $_POST[$parameter] : $default;
	}

	public static function rq($parameter, $default = NULL) {
		return isset($_REQUEST[$parameter]) ? $_REQUEST[$parameter] : $default;
	}

	public static function session($parameter, $default = NULL) {
		return isset($_SESSION[$parameter]) ? $_SESSION[$parameter] : $default;
	}

	public static function server($parameter, $default = NULL) {
		return isset($_SERVER[$parameter]) ? $_SERVER[$parameter] : $default;
	}

}