<?php

final class ErrorController extends Controller {
	
	/**
	 * @Public
	 * @Title "Forbidden"
	 * @Page "../_error.phtml"
	 * @ResponseCode 403
	 */
	public function forbidden() {
		$this->page->title = "403 Forbidden";
		$this->page->message = "You have insufficient privileges to access this page.";
	}

	/**
	 * @Public
	 * @Title "Not found"
	 * @Page "../_error.phtml"
	 * @ResponseCode 404
	 */
	public function notFound() {
		$this->page->title = "404 Not found";
		$this->page->message = "The page you are requesting does not exits.";
	}

	/**
	 * @Public
	 * @Title "Not acceptable"
	 * @Page "../_error.phtml"
	 * @ResponseCode 406
	 */
	public function notAcceptable() {
		$this->page->title = "406 Not acceptable";
		$this->page->message = "The content type you requested could not be served.";
	}

}