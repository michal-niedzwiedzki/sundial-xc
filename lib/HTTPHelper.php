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

	public static function mockPost(array $params) {
		$_POST = $params;
		$_REQUEST = array_merge((array)$_REQUEST, $params);
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

	public static function setResponseCode($code) {
		$messages = array(
			100 => "Continue",
			101 => "Switching Protocols",
			102 => "Processing",
			200 => "OK",
			201 => "Created",
			202 => "Accepted",
			203 => "Non-Authoritative Information",
			204 => "No Content",
			205 => "Reset Content",
			206 => "Partial Content",
			207 => "Multi-Status",
			208 => "Already Reported",
			226 => "IM Used",
			300 => "Multiple Choices",
			301 => "Moved Permanently",
			302 => "Found",
			303 => "See Other",
			304 => "Not Modified",
			305 => "Use Proxy",
			306 => "Reserved",
			307 => "Temporary Redirect",
			400 => "Bad Request",
			401 => "Unauthorized",
			402 => "Payment Required",
			403 => "Forbidden",
			404 => "Not Found",
			405 => "Method Not Allowed",
			406 => "Not Acceptable",
			407 => "Proxy Authentication Required",
			408 => "Request Timeout",
			409 => "Conflict",
			410 => "Gone",
			411 => "Length Required",
			412 => "Precondition Failed",
			413 => "Request Entity Too Large",
			414 => "Request-URI Too Long",
			415 => "Unsupported Media Type",
			416 => "Requested Range Not Satisfiable",
			417 => "Expectation Failed",
			429 => "Too Many Requests",
			422 => "Unprocessable Entity",
			423 => "Locked",
			424 => "Failed Dependency",
			426 => "Upgrade Required",
			500 => "Internal Server Error",
			501 => "Not Implemented",
			502 => "Bad Gateway",
			503 => "Service Unavailable",
			504 => "Gateway Timeout",
			505 => "HTTP Version Not Supported",
			507 => "Insufficient Storage",
			508 => "Loop Detected",
			510 => "Not Extended",
		);
		header("HTTP/1.1 {$messages[$code]}", TRUE, $code);
	}

}