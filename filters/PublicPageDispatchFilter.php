<?php

final class PublicPageDispatchFilter implements DispatchFilter {

	public function before() {
		if (!Dispatcher::getInstance()->getAnnotation("Public")) {
			cMember::getCurrent()->MustBeLoggedOn();
		}
	}

	public function after() { }

}
