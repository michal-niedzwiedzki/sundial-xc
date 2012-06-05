<?php

final class DebugViewDispatchFilter implements DispatchFilter {

	public function before() { }

	public function after() {
		Debug::log("End", Debug::DEBUG);
		PageView::getInstance()->setDebug(LogWriterScreen::getLog());
	}

}
