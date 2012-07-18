<?php

class cDateTime {

	public $year;
	public $month;
	public $day;
	public $hour;
	public $minute;
	public $second;

	public function cDateTime($date_str, $redirect = true) {
		// TODO: There is a problem with timestamp() for dates much earlier than 1970.
		if (!$date_str) {
			return FALSE;
		}
		if (is_numeric($date_str)) {
			// Probably came direct from the database -- try to format
			$date_str = substr($date_str, 0, 4)."-".substr($date_str, 4, 2)."-".substr($date_str, 6, 2)." ".substr($date_str, 8, 2).":".substr($date_str, 10, 2).":".substr($date_str, 12, 2);
		}
		if (($timestamp = strtotime($date_str)) == -1) {
			if (!$redirect) {
				return FALSE;
			}
			cError::getInstance()->Error("Date format invalid in cDateTime.");
			return FALSE;
		}
		$this->year = date("Y", $timestamp);
		$this->month = date("m", $timestamp);
		$this->day = date("d", $timestamp);
		$this->hour = date("H", $timestamp);
		$this->minute = date("i", $timestamp);
		$this->second = date("s", $timestamp);
		return TRUE;
	}

	public function Set($datestr) {
		return $this->cDateTime($datestr);
	}

	public function MySQLTime() {
		return $this->year . $this->month . $this->day . $this->hour . $this->minute . $this->second;
	}

	public function MySQLDate() {
		return $this->year . $this->month . $this->day;
	}

	public function StandardDate() {
		return $this->year ."/". $this->month ."/". $this->day;
	}

	public function ShortDate() {
		return MONTH_FIRST
			? sprintf("%d/%d/%s", $this->month, $this->day, substr($this->year, 2, 2))
			: sprintf("%d/%d/%s", $this->day, $this->month, substr($this->year, 2, 2));
	}

	public function Timestamp () {
		return strtotime($this->year ."/". $this->month ."/". $this->day ." ". $this->hour .":". $this->minute .":". $this->second);
	}

	public function DateArray() {
		return array ("d" => $this->day, "F" => $this->month, "Y" => $this->year);
	}

	public function MinutesAgo () {
		return floor((strtotime("now") - $this->Timestamp()) / 60);
	}

	public function DaysAgo () {
		return floor((strtotime("now") - $this->Timestamp()) / 86400);
	}

}

?>