<?php

final class ContentTypeDispatchFilter implements DispatchFilter {

	public function before() {
		$config = Config::getInstance();
		$accept = HTTPHelper::get("content_type");
		$accept or $accept = HTTPHelper::server("HTTP_ACCEPT");
		$accept = preg_replace("/,.*/", "", $accept);
		Debug::log("Detected content type: " . $accept, Debug::DEBUG);

#		if (!isset($config->mime->$accept) or $config->mime->$accept === NULL) {
#			Dispatcher::getInstance()->configure("error", "notAcceptable");
#			return;
#		}
#		$ext = $config->mime->$accept;
#		Debug::log("Using template extension $ext", Debug::DEBUG);
#		View::setExtension("{$ext}.phtml");

		$controller = Dispatcher::getInstance()->getController();
		$controller->page = new JSONView();
		header("Content-Type: application/json");
	}

	public function after() {
		// FIXME: add output renderer selection/validation
	}

}