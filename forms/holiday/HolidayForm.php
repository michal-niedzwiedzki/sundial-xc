<?php

final class HolidayForm extends Form {

	public function __construct($memberId, $adminMode) {
		parent::__construct();

		$today = getdate();
		$options = array(
			"language"=> "es",
			"format" => "dFY",
			"minYear" => $today["year"],
			"maxYear" => $today["year"] + 5
		);

		$this->addElement("hidden", "mode", $adminMode ? "admin" : "self");
		$this->addElement("hidden", "member_id", $memberId);
		$this->addElement("date", "return_date", "Fecha de reactivación?", $options);
		$this->addElement("submit", "btnSubmit", "Desactivar");

		$this->registerRule("verify_future_date", "function", "verifyFutureDate", $this);
		$this->addRule("return_date", "Tienes que elegir una fecha futura", "verify_future_date");
		$this->registerRule("verify_valid_date", "function", "verifyValidDate", $this);
		$this->addRule("return_date", "Fecha is invalida", "verify_valid_date");
	}

	public function verifyFutureDate($value) {
		$dateStr = $value["Y"] . "/" . $value["F"] . "/" . $value["d"];
		return strtotime($dateStr) > time();
	}

	public function verifyValidDate($value) {
		return checkdate($value["F"], $value["d"], $value["Y"]);
	}

}