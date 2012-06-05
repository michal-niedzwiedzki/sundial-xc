<?php

final class PageTitleDispatchFilter implements DispatchFilter {

	public function before() {
		$title = Dispatcher::getInstance()->getAnnotation("Title");
		$title and PageView::getInstance()->title = $title;
	}

	public function after() { }

}