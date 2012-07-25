<?php

/**
 * Dispatch filter for setting up controller and action from RESTful URL
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class RestRouteDispatchFilter implements DispatchFilter {

	public function before() {
		$method = HTTPHelper::method();
		$pathInfo = HTTPHelper::pathInfo();
		$parts = explode("/", trim($pathInfo, "/"));

		$controllerName = array_shift($parts);
		$controllerName or $controllerName = "home";
		$actionName = empty($parts) ? "index" : strtolower($method);
		$args = $parts;

		Dispatcher::getInstance()->configure($controllerName, $actionName, $args);
	}

	public function after() { }

}