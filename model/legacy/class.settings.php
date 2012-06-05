<?php

class cSettings {

	var $theSettings = Array(); // Current site settings are stored here
	var $current = Array();

	// Constructor - we want to get current site settings
	function cSettings() {

		$this->getCurrent();
	}

	// Get and store current site settings
	function getCurrent() {
		$this->retrieve();
		//$this->current = Array();
		// Store current settings in easily accessible constants
		$stngs = $this->theSettings;
		$sql_data = array();

		foreach($stngs as $s => $ss) {
				if ($ss->typ=='bool') {
					if (strtolower($ss->current_value)=='false') {
						$ss->current_value = "";
					} else {
						$ss->current_value = 1;
					}
					define("".$ss->name."",((boolean) $ss->current_value));	
				}
				else if ($ss->typ=='int')
					define("".$ss->name."",((int) $ss->current_value));
				else
					define("".$ss->name."","".$ss->current_value."");
		}
	}

	// Retrieve current settings
	protected function retrieve() {
		try {
			$sql = "SELECT * FROM " . DB::SETTINGS;
			$rows = PDOHelper::fetchAll($sql);
		} catch (Exception $e) {
			$rows = array();
		}
		foreach ($rows as $i => $row) {
			$row = (object)$row;
			$row->current_value || $row->current_value = $row->default_value;
			$this->theSettings[] = $row;

		}

	}

	function split_options($wh) {
		$options = explode(",",$wh);
		return $options;
	}

	// Save new settings
	function update() {
		$this->retrieve();
		$stngs = $this->theSettings;
		$sql_ata = array();

		foreach($stngs as $s => $ss) {
			$sql_data[''.$ss->name.''] = ''.$_REQUEST["".$ss->name.""].'';
		}

		foreach ($sql_data as $column => $value) {
			$result = PDOHelper::update(DB::SETTINGS, array("current_value" => $value), "name = :column", array("column" => $column));
			if (!$result) {
				return "<font color=red>Update failed!</font> Failed to update settings: $column";
			}
		}
		$this->getCurrent(); // Refresh settings in current memory with new updated settings
		return "<font color=green>Settings updated successfully.</font>";
	}
}

$site_settings = new cSettings();

?>