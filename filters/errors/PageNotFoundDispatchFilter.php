<?php

/**
 * Dispatch filter checking for 404 errors
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class PageNotFoundDispatchFilter implements DispatchFilter {

	public function before() {
		$dispatcher = Dispatcher::getInstance(); 
		if (!method_exists($dispatcher->getController(), $dispatcher->getActionName())) {
			$dispatcher->configure('error', 'notFound');
		}
	}

	public function after() { }

}