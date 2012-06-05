<?php

final class ContentTypeDispatchFilter implements DispatchFilter {

	public function before() {
		$config = Config::getInstance();
$_SERVER["HTTP_ACCEPT"] = 'text/json';
		$accept = HTTPHelper::server("HTTP_ACCEPT");
		if (NULL === $config->contentTypes->$accept) {
			Dispatcher::getInstance()->configure("error", "notAcceptable");
		}
		Debug::log("Detected content type: " . $config->contentTypes->$accept, Debug::DEBUG);
	}

}