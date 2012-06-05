<?php

class cLogEntry {

	var $log_id;
	var $log_date;
	var $admin_id; // usually a member_id, but not always
	var $category; // See inc.global.php for constants used in this field
	var $action;	// See inc.global.php for constants used in this field
	var $ref_id; // usually refences a trade_id, feedback_id, or similar
	var $note;

	public function cLogEntry ($category, $action, $ref_id, $note = null) {
		$user = cMember::getCurrent();
		$this->category = $category;
		$this->action = $action;
		$this->ref_id = $ref_id;
		$this->note = $note;
		$this->admin_id = $user->member_id;
	}

	public function SaveLogEntry() {
		$params = array(
			"admin_id" => $this->admin_id,
			"category" => $this->category,
			"action" => $this->action,
			"ref_id" => $this->ref_id,
			"log_date" => date("Y-m-d"),
			"note" => $this->note
		);
		$out = PDOHelper::insert(DB::LOGGING, $params);
		if ($out) {
			$this->log_date = $params["log_date"];
			return TRUE;
		}
		return FALSE;
	}
}

class cLogStatistics {

	public function MostRecentLog ($category, $action = NULL) {
		$tableName = DB::LOGGING;
		if ($action) {
			$sql = "SELECT max(log_date) AS max_log_date FROM $tableName WHERE category = :category";
			$row = PDOHelper::fetchRow($sql, array("category" => $category));
		} else {
			$sql = "SELECT max(log_date) AS max_log_date FROM $tableName WHERE category = :category AND action = :action";
			$row = PDOHelper::fetchRow($sql, array("category" => $category, "action" => $action));
		}
		return empty($row) ? FALSE : new cDateTime($row["max_log_date"]);
	}

}

// System events are processes which only need to run periodically,
// and so are run at intervals rather than weighing the system
// down by running them each time a particlular page is loaded.

class cSystemEvent {

	var $event_type; // See inc.global.php for constants used in this field
	var $event_interval; // See inc.config.php for interval settings

	public function cSystemEvent($event_type, $event_interval = NULL) {
		global $SYSTEM_EVENTS;
		$this->event_type = $event_type;
		if($event_interval) {
			$this->event_interval = $event_interval; // use explicit interval
		} else {
			$this->event_interval = $SYSTEM_EVENTS[$event_type]; // use defined interval
		}
	}

	public function TimeForEvent() {
		$logs = new cLogStatistics();
		$last_event = $logs->MostRecentLog($this->event_type);
		if ($last_event->MinutesAgo() >= $this->event_interval) {
			return TRUE;
		} elseif ($last_event == "") { // Never run before, so now's as good a time as any
			return TRUE;
		}
		return FALSE;
	}

	public function LogEvent() {
		$e = new cLogEntry($this->event_type, $this->event_type, $this->event_type);
		$e->admin_id = "EVENT_SYSTEM";
		$e->SaveLogEntry();
	}

}