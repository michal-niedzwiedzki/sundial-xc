<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class ListingControllerTest extends PHPUnit_Framework_TestCase {

	const LISTING_TITLE = "Herding cats";
	const LISTING_DESCRIPTION = "Meow";

	private $user;
	private $category;
	private $dispatcher;

	public function setUp() {
		$user = UsersMother::create();
		$user->Login($user->getId(), cMember::DEFAULT_PASSWORD);
		$this->user = cMember::getCurrent();
		$this->category = CategoriesMother::create("Animals care " . microtime(TRUE));
		$this->dispatcher = Dispatcher::getInstance();
	}

	/**
	 * Test successful listing creation in user mode
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testCreate_okInUserMode() {
		// mock request data
		HTTPHelper::mockPost(array(
			"csrf" => CSRF,
			"mode" => "self",
			"memberId" => $this->user->getId(),
			"type" => cListing::CODE_WANTED,
			"title" => self::LISTING_TITLE,
			"description" => self::LISTING_DESCRIPTION,
			"category" => $this->category->getId(),
			"submit" => "submit",
		));

		// run controller
		$this->dispatcher->configure("listing", "create")->dispatch();

		// test if message appeared
		$this->assertTrue(0 < strpos(PageView::getInstance()->__toString(), "El nuevo servicio ha sido creado."));

		// test if listing created
		$listing = new cListing();
		$listing->LoadListing(self::LISTING_TITLE, $this->user->getId(), cListing::CODE_WANTED);
		$this->assertEquals(self::LISTING_DESCRIPTION, $listing->description);
		$this->assertEquals($this->category->getId(), $listing->category->getId());
	}

	/**
	 * Test successful listing update in user mode
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testEdit_okInUserMode() {
		// mock listing and category
		$listing = ListingsMother::offered(self::LISTING_TITLE, self::LISTING_DESCRIPTION, $this->category->getId(), $this->user->getId());
		$newDescription = "Woof woof!";
		$newCategory = CategoriesMother::create("Herding dogs " . microtime(TRUE));

		// mock request data
		HTTPHelper::mockPost(array(
			"csrf" => CSRF,
			"mode" => "self",
			"memberId" => $this->user->getId(),
			"type" => cListing::CODE_OFFERED,
			"title" => self::LISTING_TITLE,
			"description" => $newDescription,
			"category" => $newCategory->getId(),
			"submit" => "submit",
		));

		// run controller
		$this->dispatcher->configure("listing", "edit")->dispatch();

		// test if message appeared
#		$this->assertTrue(0 < strpos(PageView::getInstance()->__toString(), "El servicio ha sido guardado."));

		// test if listing created
		$listing = new cListing();
		$listing->LoadListing(self::LISTING_TITLE, $this->user->getId(), cListing::CODE_OFFERED);
		$this->assertEquals($newDescription, $listing->description);
		$this->assertEquals($newCategory->getId(), $listing->category->getId());
	}

}