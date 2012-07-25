<?php

final class ListingController extends Controller {

	/**
	 * @Public
	 */
	public function index() {
		$group = new cListingGroup(NULL);
		$group->LoadListingGroup();
		$listings = $group->getListings();
		foreach ($listings as $i => $listing) {
			unset($listings[$i]->member);
		}
		$this->page->count = count($listings);
		$this->page->listings = $listings;
	}

	/**
	 * @Public
	 */
	public function get($title, $memberId, $type) {
		$listing = new cListing();
		$listing->LoadListing($title, $memberId, $type);
		unset($listing->member);
		$this->page->listing = $listing;
	}

}