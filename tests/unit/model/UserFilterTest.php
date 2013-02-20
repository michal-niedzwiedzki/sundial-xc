<?php

require_once dirname(__FILE__) . "/../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * Unit test for class UserFilter
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class UserFilterTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test filtering by user id
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testId() {
		$filter = new UserFilter();
		$f = $filter->id(12345);

		list ($where, $params) = $filter->get();
		$this->assertSame("id = :id", $where);
		$this->assertSame(array("id" => 12345), $params);
		$this->assertSame($filter, $f);
	}

	/**
	 * Test filtering by phrase
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testText() {
		$filter = new UserFilter();
		$f = $filter->text("Aqq");

		list ($where, $params) = $filter->get();
		$this->assertSame("(login LIKE '%:login%' OR name LIKE '%:shortName%' OR name LIKE '%:name%')", $where);
		$this->assertSame(array("login" => "Aqq", "shortName" => "Aqq", "name" => "Aqq"), $params);
		$this->assertSame($filter, $f);
	}

	/**
	 * Test filtering active users
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testActive() {
		$filter = new UserFilter();
		$f = $filter->active();

		list ($where, $params) = $filter->get();
		$this->assertSame("state = :state", $where);
		$this->assertSame(array("state" => User::STATE_ACTIVE), $params);
		$this->assertSame($filter, $f);
	}

	/**
	 * Test filtering inactive users
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testInactive() {
		$filter = new UserFilter();
		$f = $filter->inactive();

		list ($where, $params) = $filter->get();
		$this->assertSame("state <> :state", $where);
		$this->assertSame(array("state" => User::STATE_ACTIVE), $params);
		$this->assertSame($filter, $f);
	}

	/**
	 * Test filtering users by state
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testState() {
		$filter = new UserFilter();
		$f = $filter->state(User::STATE_EXPIRED);

		list ($where, $params) = $filter->get();
		$this->assertSame("state = :state", $where);
		$this->assertSame(array("state" => User::STATE_EXPIRED), $params);
		$this->assertSame($filter, $f);
	}

	/**
	 * Test ordering users by id
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testOrderById() {
		$filter = new UserFilter();
		$f = $filter->orderById(TRUE);

		list ($where, $params) = $filter->get();
		$this->assertSame("TRUE ORDER BY id ASC", $where);
		$this->assertSame($filter, $f);

		$filter = new UserFilter();
		$f = $filter->orderById(FALSE);

		list ($where, $params) = $filter->get();
		$this->assertSame("TRUE ORDER BY id DESC", $where);
		$this->assertSame($filter, $f);
	}

	/**
	 * Test ordering users by full name
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testOrderByName() {
		$filter = new UserFilter();
		$f = $filter->orderByName(TRUE);

		list ($where, $params) = $filter->get();
		$this->assertSame("TRUE ORDER BY name ASC", $where);
		$this->assertSame($filter, $f);

		$filter = new UserFilter();
		$f = $filter->orderByName(FALSE);

		list ($where, $params) = $filter->get();
		$this->assertSame("TRUE ORDER BY name DESC", $where);
		$this->assertSame($filter, $f);
	}

}