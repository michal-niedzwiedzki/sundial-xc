<?php

final class ContentTypeDispatchFilter implements DispatchFilter {

	public function before() {
		$config = Config::getInstance();
		$accept = HTTPHelper::get("content_type");
		$accept or $accept = HTTPHelper::server("HTTP_ACCEPT");
		$accept = preg_replace("/,.*/", "", $accept);

		if (!isset($config->contentTypes->$accept) or $config->contentTypes->$accept === NULL) {
			Dispatcher::getInstance()->configure("error", "notAcceptable");
			return;
		}
		$ext = $config->contentTypes->$accept;
		View::setExtension("{$ext}.phtml");
		Debug::log("Detected content type: " . $config->contentTypes->$accept, Debug::DEBUG);
	}

	public function after() {
		// FIXME: add output renderer selection/validation
	}

}