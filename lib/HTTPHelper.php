<?php

final class HTTPHelper {

	public static function method() {
		return isset($_SERVER["REQUEST_METHOD"]) ? strtoupper($_SERVER["REQUEST_METHOD"]) : "";
	}

	public static function pathInfo() {
		return isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "";
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

	public static function redirect($to) {
		if (Debug::hasProblems()) {
			Debug::log("Problems exist that prevent redirect to {$to}", Debug::INFO);
 			return;
		}
		header("HTTP/1.1 303 See Other", TRUE, 303);
		header("Location: $to");
		exit();
	}

}