<?php

final class TradeReverseForm extends Form {

	public function __construct(array $trades) {
		parent::__construct();
		$this->addElement("select", "trade_id", "Elegir el intercambio que quieres deshacer", $trades);
		$this->addElement("textarea", "description", NULL, array("cols" => 50, "rows" => 2, "wrap" => "soft", "maxlength" => 75));
		$this->addElement("submit", "btnSubmit", "Deshacer");
	}

}