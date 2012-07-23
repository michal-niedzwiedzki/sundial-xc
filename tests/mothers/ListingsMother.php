<?php

final class ListingsMother {

	public static function wanted($title, $description, $categoryId, $memberId, array $args = array()) {
		return self::createListing($title, $description, $categoryId, $memberId, cListing::CODE_WANTED, $args);
	}

	public static function offered($title, $description, $categoryId, $memberId, array $args = array()) {
		return self::createListing($title, $description, $categoryId, $memberId, cListing::CODE_OFFERED, $args);
	}

	private static function createListing($title, $description, $categoryId, $memberId, $type, array $args = array()) {
		$row = array(
			"title" => $title,
			"description" => $description,
			"category_code" => $categoryId,
			"member_id" => $memberId,
			"rate" => NULL,
			"status" => isset($args["status"]) ? $args["status"] : "A",
			"posting_date" => isset($args["posting_date"]) ? $args["posting_date"] : date("Y-m-d h:i:s"),
			"expire_date" => isset($args["expire_date"]) ? $args["expire_date"] : NULL,
			"reactivate_date" => isset($args["reactivate_date"]) ? $args["reactivate_date"] : NULL,
			"type" => $type
		);
		PDOHelper::insert(DB::LISTINGS, $row);
		$listing = new cListing();
		$listing->LoadListing($title, $memberId, $type);
		return $listing;
	}

}