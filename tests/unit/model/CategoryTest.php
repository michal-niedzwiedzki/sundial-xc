<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * Unit test for class Category
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class CategoryTest extends PHPUnit_Framework_TestCase {

	private function generateDescription() {
		return "Category " . microtime(TRUE);
	}

	/**
	 * Test category creation
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testSaveNewCategory() {
		$category = new Category(array("description" => $this->generateDescription()));
		$this->assertTrue((boolean)$category->save());
		$this->assertTrue($category->id > 0);
	}

	/**
	 * Test category update
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testSaveExistingCategory() {
		$category = new Category(array("description" => $this->generateDescription()));
		$this->assertTrue((boolean)$category->save());
		$category->description .= "_new";
		$this->assertTrue((boolean)$category->save());
	}

	/**
	 * Test getting single category by id
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testGetById() {
		$description = $this->generateDescription();
		$category = new Category(array("description" => $description));
		$this->assertTrue((boolean)$category->save());
		$id = $category->id;
		unset($category);

		$category = Category::getById($id);
		$this->assertSame($description, $category->description);
	}

}