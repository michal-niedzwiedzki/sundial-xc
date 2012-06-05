<?php

class cTradeStats extends cTradeGroup {

	var $total_trades = 0;
	var $total_units = 0;
	var $most_recent = ""; // Will be an object of class cDateTime

	public function __construct($member_id = "%", $from_date = LONG_LONG_AGO, $to_date = FAR_FAR_AWAY) {
		parent::__construct($member_id, $from_date, $to_date);
		if (!$this->LoadTradeGroup())
			return;

		foreach($this->trade as $trade) {
			if ($trade->type == TRADE_REVERSAL or $trade->status == TRADE_REVERSAL)
				continue; // skip reversed trades

			$this->total_trades += 1;
			$this->total_units += $trade->amount;

			if($this->most_recent == "") {
				$this->most_recent = new cDateTime($trade->trade_date);
			} elseif ($this->most_recent->MySQLDate() < $trade->trade_date) {
				$this->most_recent->Set($trade->trade_date);
			}
		}
	}

}