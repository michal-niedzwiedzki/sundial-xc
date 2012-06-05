<?php

final class cError {

	const ERROR_ARRAY_SEVERITY = 0;
	const ERROR_ARRAY_MESSAGE = 1;
	const ERROR_ARRAY_FILE = 2;
	const ERROR_ARRAY_LINE = 3;

	const ERROR_SEVERITY_INFO = 1;
	const ERROR_SEVERITY_LOW = 2;
	const ERROR_SEVERITY_MED = 3;
	const ERROR_SEVERITY_HIGH = 4;
	const ERROR_SEVERITY_STOP = 5;

	const ERROR_MESSAGE_DENIED = "access-denied";

	public $retval;
	public $retobj;
	public $arrErrors;

	protected static $instance;

	protected function __construct() {
		$this->arrErrors = array();
		if (isset($_SESSION["errors_saved"])) {
			$this->arrErrors = $_SESSION["errors_saved"];
			unset ($_SESSION["errors_saved"]); // don't want the errors to keep appearing...
		}
	}

	public static function getInstance() {
		self::$instance or self::$instance = new cError();
		return self::$instance;
	}

	public function Error($message, $severity = 0, $file = "", $line = 0) {
		$severity or $severity = self::ERROR_SEVERITY_LOW;
		$this->arrErrors[] = array(
			self::ERROR_ARRAY_MESSAGE => $message,
			self::ERROR_ARRAY_SEVERITY => $severity,
			self::ERROR_ARRAY_FILE => $file,
			self::ERROR_ARRAY_LINE => $line
		);
		if ($severity == self::ERROR_SEVERITY_STOP) {
			$this->DoStopError();
		}
	}

	public function SaveErrors() {
		// we're about to redirect, but want to remember the errors, so put them in session temporarily
		$_SESSION["errors_saved"] = $this->arrErrors;
	}

	public function DoStopError() {
		die($this->ErrorBox());
	}

	public function ErrorBox() {
		$master = PageView::getInstance();
		$master->displayError(implode("\n", $this->arrErrors));
	}

	public function ReturnValue($message, $obj = "") {
		$this->retval = $message;
		$this->retobj = $obj;
	}

}