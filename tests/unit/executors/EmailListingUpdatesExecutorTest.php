<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class EmailListingUpdatesExecutorTest extends PHPUnit_Framework_TestCase {

	public function testExecute() {
		$user = UsersMother::create();
		$category = CategoriesMother::create("Fun");
		$offered = ListingsMother::offered("Walking the dog", "", $category->getId(), $user->getId());
		$wanted = ListingsMother::wanted("Lawn mowing", "", $category->getId(), $user->getId());

		// run executor
		$executor = new EmailListingUpdatesExecutor(array("interval" => 7));
		$executor->execute();

		// test messages created by executor
		$this->assertTrue($executor->getMessage() instanceof EmailMessage);
	}

}