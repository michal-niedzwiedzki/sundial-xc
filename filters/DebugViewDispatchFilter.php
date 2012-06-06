<?php

/**
 * Dispatch filter outputting debug messages to the screen
 *
 * In order to work LogWriterScreen needs to be specified in config.
 * Logged messages are written at the bottom of the page.
 * Note that errors that occur after method after() of this filter is run
 * will not be outputted to screen.
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class DebugViewDispatchFilter implements DispatchFilter {

	public function before() { }

	public function after() {
		Debug::log("End", Debug::DEBUG);
		PageView::getInstance()->setDebug(LogWriterScreen::getLog());
	}

}