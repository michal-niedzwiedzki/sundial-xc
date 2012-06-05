<?php

final class TimeframeChooserForm extends Form {
	
	public function __construct($action, $today = NULL) {
		parent::__construct();

		$today or $today = getdate();
		$options = array(
			"language" => "es",
			"format" => "dFY",
			"minYear" => $today["year"] - 3,
			"maxYear" => $today["year"]
		);

		$this->addElement("hidden", "action", $action);
		$this->addElement("date", "from", "¿Desde fecha?", $options);
		$this->addElement("date", "to", "¿Hasta?", $options);
		$this->addElement("submit", "btnSubmit", "Ver resultado");

		$date = array("Y" => $today["year"], "F" => $today["mon"], "d" => $today["mday"]);
		$this->setDefaults(array("from" => $date, "to" => $date));
	}

}