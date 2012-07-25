<?php

final class ErrorController extends Controller {

	/**
	 * @Public
	 * @ResponseCode 403
	 */
	public function forbidden() { }

	/**
	 * @Public
	 * @ResponseCode 404
	 */
	public function notFound() { }

	/**
	 * @Public
	 * @ResponseCode 406
	 */
	public function notAcceptable() { }

}