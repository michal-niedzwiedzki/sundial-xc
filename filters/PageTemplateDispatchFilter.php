<?php

final class PageTemplateDispatchFilter implements DispatchFilter {

	public function before() {
		$dispatcher = Dispatcher::getInstance();
		
		// discover page template based on @Page annotation or by obeying naming convention 
		$pageAnnotation = $dispatcher->getAnnotation("Page");
		if ($pageAnnotation) {
			$pageTemplate = "pages/{$pageAnnotation}";
		} else {
			$pageTemplate = "pages/" . $dispatcher->getControllerName() . "/" . $dispatcher->getActionName() . ".phtml";
		}

		// check if template file exists
		if (file_exists(ROOT_DIR . "/templates/" . $pageTemplate)) {
			Debug::log("Found page $pageTemplate", Debug::INFO);
			$controller = $dispatcher->getController();
			$controller->page = new View($pageTemplate);
			PageView::getInstance()->displayPage($controller->page);
		} else {
			Debug::log("Page $pageTemplate not present", Debug::WARNING);
		}
	}

	public function after() { }

}