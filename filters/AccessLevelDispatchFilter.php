<?php

final class AccessLevelDispatchFilter implements DispatchFilter {

	public function before() {
		$requisiteLevel = (int)Dispatcher::getInstance()->getAnnotation("Level");
		if ($requisiteLevel) {
			$actualLevel = (int)cMember::getCurrent()->member_role;
			if ($requisiteLevel and $actualLevel < $requisiteLevel) {
				Dispatcher::getInstance()->configure("error", "forbidden");
			}
		}
	}

	public function after() { }

}