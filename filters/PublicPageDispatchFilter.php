<?php

/**
 * Dispatch filter checking @Public annotation on controller method
 *
 * Provides access to annotated controller actions for not logged in uses.
 * Actions not annotated as @Public will cause redirect to 403 page.
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class PublicPageDispatchFilter implements DispatchFilter {

	public function before() {
		if (!Dispatcher::getInstance()->getAnnotation("Public")) {
			cMember::getCurrent()->MustBeLoggedOn();
		}
	}

	public function after() { }

}