<?php

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