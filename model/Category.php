<?php

/**
 * Category
 *
 * Holds offers.
 * Can be retrieved from or stored into database.
 *
 * @TableName "categories"
 * @PrimaryKey "category_id"
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class Category {

	/**
	 * User primary key or NULL when not yet saved
	 * @var int|NULL
	 * @Column "category_id"
	 */
	public $id;

	/**
	 * Foreign key to parent category or NULL when top level category
	 * @var int|NULL
	 * @Column "parent_id"
	 */
	public $parentId;

	/**
	 * Category name
	 * @var string
	 * @Column "description"
	 * @NotNull
	 */
	public $description;

	/**
	 * Constructor
	 *
	 * @param array $a database row as hash
	 */
	public function __construct(array $a = array()) {
		empty($a) or Persistence::revive($a, $this);
	}

	/**
	 * Return category by primary key or NULL
	 *
	 * @param int $id
	 * @return Category|NULL
	 */
	public static function getById($id) {
		$sql = "SELECT * FROM categories WHERE category_id = :id";
		$row = PDOHelper::fetchRow($sql, array("id" => $id));
		if (empty($row)) {
			return NULL;
		}
		return new Category($row);
	}

	/**
	 * Return categories by parent id
	 *
	 * @param int $id
	 * @return Category[]
	 */
	public static function getByParentId($id) {
		$sql = "SELECT * FROM categories WHERE parent_id = :id ORDER BY description";
		$rows = PDOHelper::fetchAll($sql, array("id" => $id));
		$categories = array();
		foreach ($rows as $row) {
			$categories[$row["category_id"]] = new Category($row);
		}
		return $categories;
	}

	/**
	 * Return all top level categories
	 *
	 * @return Category[]
	 */
	public static function getAllTopLevel() {
		$sql = "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY description";
		$rows = PDOHelper::fetchAll($sql, array("id" => $id));
		$categories = array();
		foreach ($rows as $row) {
			$categories[$row["category_id"]] = new Category($row);
		}
		return $categories;
	}

	/**
	 * Return all categories
	 *
	 * @return Category[]
	 */
	public static function getAll() {
		$sql = "SELECT * FROM categories ORDER BY description";
		$rows = PDOHelper::fetchAll($sql, array("id" => $id));
		$categories = array();
		foreach ($rows as $row) {
			$categories[$row["category_id"]] = new Category($row);
		}
		return $categories;
	}

	/**
	 * Save category into database and return its primary key
	 *
	 * @return int
	 */
	public function save() {
		$id = Persistence::freeze($this);
		$this->id or $this->id = $id;
		return $id;
	}

	/**
	 * Return collection of offers
	 *
	 * @param OfferFilter $filter
	 * @return Offer[]
	 */
	public function getOffers(OfferFilter $filter = NULL) {
		$filter or $filter = new OfferFilter();
		$filter->category($this->id);
		return Offer::filter($filter);
	}

}