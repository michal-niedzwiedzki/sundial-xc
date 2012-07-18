<?php

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class HTTPResponseCodeDispatchFilter implements DispatchFilter {

	public function before() { }

	public function after() {
		$code = Dispatcher::getInstance()->getAnnotation("ResponseCode");
		$code and http_response_code($code);
	}

}