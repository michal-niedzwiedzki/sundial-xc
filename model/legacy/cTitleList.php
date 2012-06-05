<?php

class cTitleList {

	// This class circumvents the cListing class for performance reasons

	var $type;
	var $type_code;  // TODO: 'type' needs to be its own class which would include 'type_code'
	var $items_per_page;  // Not using yet...
	var $current_page;   // Not using yet...

	public function __construct($type) {
		$this->type = $type;
		if($type == OFFER_LISTING)
			$this->type_code = OFFER_LISTING_CODE;
		else
			$this->type_code = WANT_LISTING_CODE;
	}

	public function MakeTitleArray($memberId = "%") {
		$tableName = DB::LISTINGS;
		$sql = "
			SELECT DISTINCT title FROM $tableName
			WHERE member_id LIKE :id AND type = :type
			ORDER BY title
		";
		$out = PDOHelper::fetchAll($sql, array("id" => $memberId, "type" => $this->type_code));
		$titles = array();
		foreach ($out as $i => $row) {
			$titles[] = $row["title"];
		}
		empty($titles) && $titles[] = "";
		return $titles;
	}

	public function DisplayMemberListings($member) {
		$tableName = DB::LISTINGS;
		$sql = "SELECT title FROM $tableName WHERE member_id = :id AND type = :type ORDER BY title";
		$out = PDOHelper::fetchAll($sql, array("id" => $member->member_id, "type" => $this->type_code));

		$list = new View("tables/listings-edit.phtml");
		$list->rows = $out;
		$list->memberId = $member->member_id;
		$list->type = $this->type;
		$list->mode = HTTPHelper::rq("mode");
		return $list;
	}

	public function getCount($memberId) {
		$tableName = DB::LISTINGS;
		$sql = "SELECT count(*) AS c FROM $tableName WHERE member_id = :id AND type = :type ORDER BY title";
		return (int)PDOHelper::fetchCell("c", $sql, array("id" => $memberId, "type" => $this->type_code));
	}

}