<?php

/**
 * Dispatch filter for setting up controller and action based on path info
 *
 * Inspects path info and based on it configures dispatcher to route request
 * to certain controller action.
 * 
 * Underscore sign is used to set apart controller name from action name.
 * Action "index" is used when no underscore. HomeController is the default one.
 * - "/listings_found.php" - ListingsController::found()
 * - "/listings.php" - ListingsController::index()
 * - "/" - HomeController::index()
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class PathInfoDispatchFilter implements DispatchFilter {

	public function before() {
		$pathInfo = isset($_SERVER["PATH_INFO"])
			? trim(str_replace(".php", "", $_SERVER["PATH_INFO"]), "/")
			: "home";
		if (FALSE === ($pos = strpos($pathInfo, "_"))) {
			$controllerName = $pathInfo;
			$actionName = "index";
		} else {
			$controllerName = substr($pathInfo, 0, $pos);
			$actionName = substr($pathInfo, $pos + 1);
		}
		Dispatcher::getInstance()->configure($controllerName, $actionName);
	}

	public function after() { }

}