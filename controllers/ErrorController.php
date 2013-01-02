<?php

final class ErrorController extends Controller {

	/**
	 * @Public
	 * @Title "Forbidden"
	 * @Page "../_error"
	 * @ResponseCode 403
	 */
	public function forbidden() {
		$this->view->title = "403 Forbidden";
		$this->view->message = "You have insufficient privileges to access this page.";
	}

	/**
	 * @Public
	 * @Title "Not found"
	 * @Page "../_error"
	 * @ResponseCode 404
	 */
	public function notFound() {
		$this->view->title = "404 Not found";
		$this->view->message = "The page you are requesting does not exits.";
	}

	/**
	 * @Public
	 * @Title "Not acceptable"
	 * @Page "../_error"
	 * @ResponseCode 406
	 */
	public function notAcceptable() {
		$this->view->title = "406 Not acceptable";
		$this->view->message = "The content type you requested could not be served.";
	}

}