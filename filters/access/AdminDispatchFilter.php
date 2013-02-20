<?php

/**
 * Dispatch filter checking @Admin annotation on controller method
 *
 * Guarantees that controller method is only accessible to admin users.
 * Users not having requisite access are redirected to 403 page.
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class AdminDispatchFilter implements DispatchFilter {

	public function before() {
		$adminRequired = Dispatcher::getInstance()->getAnnotation("Admin");
		$isAdmin = User::getCurrent()->isAdmin();
		if ($adminRequired and !$isAdmin) {
			Dispatcher::getInstance()->configure("error", "forbidden");
		}
	}

	public function after() { }

}