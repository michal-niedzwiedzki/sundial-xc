<?php

class ServerConfigFilter extends ConfigFilter {

	public function process(stdClass $o) {
		if (!isset($o->server)) {
			$o->server = new stdClass();
		}
		if (!isset($o->server->host)) {
			$o->server->host = HTTPHelper::server("PHP_HOST");
		}
	}

}