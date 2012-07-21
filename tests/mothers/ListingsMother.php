<?php

final class ListingsMother {

	public static function wanted($title, $description, $categoryId, $memberId) {
		return self::createListing($title, $description, $categoryId, $memberId, cListing::CODE_WANTED);
	}

	public static function offered($title, $description, $categoryId, $memberId) {
		return self::createListing($title, $description, $categoryId, $memberId, cListing::CODE_OFFERED);
	}

	private static function createListing($title, $description, $categoryId, $memberId, $type) {
		$row = array(
			"title" => $title,
			"description" => $description,
			"category_code" => $categoryId,
			"member_id" => $memberId,
			"rate" => NULL,
			"status" => "A",
			"posting_date" => date("Y-m-d h:i:s"),
			"expire_date" => NULL,
			"reactivate_date" => NULL,
			"type" => $type
		);
		PDOHelper::insert(DB::LISTINGS, $row);
		$listing = new cListing();
		$listing->LoadListing($title, $memberId, $type);
		return $listing;
	}

}