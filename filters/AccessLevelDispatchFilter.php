<?php

/**
 * Dispatch filter checking @Level annotation on controller method
 *
 * Guarantees that controller method is only accessible to users with defined access level.
 * Users not having requisite access are redirected to 403 page.
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
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