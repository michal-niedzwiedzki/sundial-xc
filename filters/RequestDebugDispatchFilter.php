<?php

final class RequestDebugDispatchFilter implements DispatchFilter {

	public function before() {
		if (HTTPHelper::method() === "POST") {
			Debug::log("POST params:\n" . print_r($_POST, TRUE), Debug::DEBUG);
		}
	}

	public function after() { }

}