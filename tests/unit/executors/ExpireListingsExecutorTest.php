<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class ExpireListingsExecutorTest extends PHPUnit_Framework_TestCase {

	public function testExecute() {
		$config = Config::getInstance();
		$maxDaysInactive = $config->legacy->MAX_DAYS_INACTIVE;
		$expirationWindow = $config->legacy->EXPIRATION_WINDOW;
		$deleteExpiredAfter = $config->legacy->DELETE_EXPIRED_AFTER;

		// create listings
		$user = UsersMother::createUserAccount("user_" . time(), "1", array("join_date" => "1980-01-01 00:00:00"));
		$category = CategoriesMother::create("Fun");
		$offered = ListingsMother::offered("Walking the dog", "", $category->getId(), $user->getId(), array("posting_date" => "1980-01-01 00:00:00"));
		$wanted = ListingsMother::wanted("Lawn mowing", "", $category->getId(), $user->getId(), array("posting_date" => "1980-01-01 00:00:00"));

		// run executor
		$executor = new ExpireListingsExecutor();
		$executor->execute();

		// re-read listings
		$offered = new cListing();
		$offered->LoadListing("Walking the dog", $user->getId(), cListing::CODE_OFFERED);
		$wanted = new cListing();
		$wanted->LoadListing("Lawn mowing", $user->getId(), cListing::CODE_WANTED);

		// test expiry date set by executor
		$now = time();
		$this->assertTrue(strtotime($offered->expire_date) > $now);
		$this->assertTrue(strtotime($wanted->expire_date) > $now);

		// test messages created by executor
		$this->assertTrue($executor->getMessage() instanceof EmailMessage);
	}

}