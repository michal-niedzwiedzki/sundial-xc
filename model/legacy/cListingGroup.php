<?php

class cListingGroup {

	var $title;
	var $listing;  // this will be an array of objects of type cListing
	var $num_listings;  // number of active offers
	var $type;
	var $type_code;

	public function __construct($type) {
		$this->type = $type;
		if ($type == OFFER_LISTING)
			$this->type_code = OFFER_LISTING_CODE;
		else
			$this->type_code = WANT_LISTING_CODE;
	}

	public function getListings() {
		return (array)$this->listing;
	}
	
	function InactivateAll($reactivate_date) {
		if (!isset($this->listing))
			return true;

		foreach ($this->listing as $listing)	{
			$current_reactivate = new cDateTime($listing->reactivate_date, false);
			if (($listing->reactivate_date == null or $current_reactivate->Timestamp() < $reactivate_date->Timestamp()) and $listing->status != EXPIRED) {
				$listing->reactivate_date = $reactivate_date->MySQLDate();
				$listing->status = INACTIVE;
				$success = $listing->SaveListing();

				if (!$success)
					cError::getInstance()->Error("Could not inactivate listing: '".$listing->title."'");
			}
		}
		return true;
	}

	function ExpireAll($expire_date) {
		if (!isset($this->listing))
			return true;

		foreach ($this->listing as $listing)	{
			$listing->expire_date = $expire_date->MySQLDate();
			$success = $listing->SaveListing(false);

			if (!$success)
				cError::getInstance()->Error("Could not expire listing: '".$listing->title."'");
		}
		return true;
	}

	public function LoadListingGroup($title = NULL, $category = NULL, $memberId = NULL, $since = NULL, $includeExpired = TRUE) {
		$this->title = $title ? $title : "%";
		$category or $category = "%";
		$memberId or $memberId = "%";
		$since or $since = "19790101000000";
		$expired = $includeExpired ? "TRUE" : "expire_date IS NULL";
		$type = $this->type_code ? strtoupper(substr($this->type_code, 0, 1)) : "%";

		// select all the member_ids for this $title
		$listingsTableName = DB::LISTINGS;
		$categoriesTableName = DB::CATEGORIES;
		$sql = "
			SELECT title, member_id FROM $listingsTableName AS l, $categoriesTableName AS c
			WHERE title LIKE :title AND l.category_code = c.category_id AND c.category_id LIKE :category
				AND type LIKE :type AND member_id LIKE :id AND posting_date >= :since AND $expired
			ORDER BY c.description, title, member_id
		";
		$out = PDOHelper::fetchAll($sql, array("title" => $this->title, "category" => $category, "type" => $type, "id" => $memberId, "since" => $since));

		// instantiate new cOffer objects and load them
		$this->num_listings = 0;
		foreach ($out as $i => $row) {
			$this->listing[$i] = new cListing();
			$this->listing[$i]->LoadListing($row["title"], $row["member_id"], $this->type_code);
			if ($this->listing[$i]->status == 'A') {
				$this->num_listings += 1;
			}
		}

		return !empty($out);
	}

	public function DisplayListingGroup($showIds = FALSE, $activeOnly = TRUE) {
		$user = cMember::getCurrent();
		$config = Config::getInstance();
		$rows = array();
		$categories = array();
		$currentCat = "";
		if (!isset($this->listing)) {
			return "Ninguno encontrado";
		}
		foreach ($this->listing as $listing) {
			if ($activeOnly and $listing->status != "A") {
				continue; // Skip inactive items
			}

			$tableName = DB::PERSONS;
			$sql = "SELECT * FROM $tableName WHERE member_id = :id";
			$row = PDOHelper::fetchRow($sql, array("id" => $listing->member_id));

			$person = $row["first_name"] . " " . $row["mid_name"];
			$profileLink = "member_summary.php?member_id=" . e($listing->member_id);
			$login = $listing->member_id;
			$listingLink = "listing_detail.php?type={$this->type}&title={$listing->title}&member_id={$listing->member_id}";
			$listingTitle = $listing->title;
			$listingDetails = $listing->description;

			// Rate
			if ($config->legacy->SHOW_RATE_ON_LISTINGS && $listing->rate) {
				$rate = $listing->rate . " " . UNITS;
			} else {
				$rate = "";
			}

			$currentCat = $listing->category->id;
			$currentTitle = $listing->category->description;
			$categories[$currentCat] = TRUE;

			array_key_exists($currentCat, $rows) || $rows[$currentCat] = array();
			$rows[$currentCat][] = array(
				"category" => $currentCat,
				"categoryTitle" => $currentTitle,
				"person" => $person,
				"profileLink" => $profileLink,
				"login" => $login,
				"listingLink" => $listingLink,
				"listingTitle" => $listingTitle,
				"listingDetails" => $listingDetails,
			);
		}

		$table = new View("listing");
		$table->data = $rows;
		$table->categories = array_keys($categories);
		return $table;
	}

}