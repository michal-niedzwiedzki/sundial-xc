<?php

/**
 * Dispatch filter for setting up HTML page "title" element from @Title annotation
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class PageTitleDispatchFilter implements DispatchFilter {

	public function before() {
		$title = Dispatcher::getInstance()->getAnnotation("Title");
		$title and PageView::getInstance()->title = $title;
	}

	public function after() { }

}