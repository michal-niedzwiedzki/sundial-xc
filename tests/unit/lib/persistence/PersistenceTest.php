<?php

require_once dirname(__FILE__) . "/../../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

require_once dirname(__FILE__) . "/mocks/PersistentMock.php";
require_once dirname(__FILE__) . "/mocks/TransformationMock.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class PersistenceMessageTest extends PHPUnit_Framework_TestCase {

	const DROP = "DROP TABLE IF EXISTS persistence_mocks";
	const CREATE = "
		CREATE TABLE persistence_mocks (
			id INT PRIMARY KEY AUTO_INCREMENT,
			full_name VARCHAR(200) NOT NULL,
			age INT DEFAULT NULL,
			wtf INT DEFAULT NULL
		) ENGINE Memory
	";

	public function setUp() {
		parent::setUp();
		$pdo = DB::getPDO();
		$pdo->exec(self::DROP);
		$pdo->exec(self::CREATE);
	}

	public function tearDown() {
		DB::getPDO()->exec(self::DROP);
		parent::tearDown();
	}

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testInsert() {
		// insert all columns
		$mock = new PersistentMock();
		$mock->name = "Mock";
		$mock->age = 5;
		$this->assertEquals(1, Persistence::freeze($mock));

		// insert default null
		$mock = new PersistentMock();
		$mock->name = "Mock";
		$this->assertEquals(2, Persistence::freeze($mock));
	}

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testUpdate() {
		// insert
		$mock = new PersistentMock();
		$mock->name = "Mock";
		$this->assertEquals(1, $id = Persistence::freeze($mock));
		$mock->id = $id;

		// update
		$mock->age = 5;
		$this->assertEquals(1, Persistence::freeze($mock));
	}

}