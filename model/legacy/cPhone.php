<?php

class cPhone {

	var $area;
	var $prefix;
	var $suffix;
	var $ext;

	public function __construct($number = NULL) {
		if (!$number) {
			return;
		}
		$ext = "";
		$number = strtolower($number);
		if ($loc = strpos($number, "x")) {
			$ext = substr($number, $loc + 1, 10);
			$number = substr($number, 0, $loc); // strip extension off the main string
			$ext = ereg_replace("t", "", $ext);
			$ext = ereg_replace("\.", "", $ext);
			$ext = ereg_replace(" ", "", $ext);
			is_numeric($ext) or $ext = "";
		}
		$number = ereg_replace("\(", "", $number);
		$number = ereg_replace("\)", "", $number);
		$number = ereg_replace("-", "", $number);
		$number = ereg_replace("\.", "", $number);
		$number = ereg_replace(" ", "", $number);
		$number = ereg_replace("e", "", $number);

		if (strlen($number) == 7) {
			$this->area = Config::getInstance()->legacy->DEFAULT_PHONE_AREA;
			$this->prefix = substr($number, 0, 3);
			$this->suffix = substr($number, 3, 4);
			$this->ext = $ext;
		} elseif (strlen($number) == 10) {
			$this->area = substr($number, 0, 3);
			$this->prefix = substr($number, 3, 3);
			$this->suffix = substr($number, 6, 4);
			$this->ext = $ext;
		}
	}

	public function TenDigits() {
		return $this->area . $this->prefix . $this->suffix;
	}

	public function SevenDigits() {
		return $this->prefix . $this->suffix;
	}

}