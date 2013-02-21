<?php

class UserFilter {

	protected $id;
	protected $active;
	protected $state;
	protected $orderBy;

	/**
	 * Set filtering by user id
	 *
	 * @param int $id
	 * @return UserFilter
	 */
	public function id($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Set filtering by phrase in login, full name, and short name
	 *
	 * @param string $phrase
	 * @return UserFilter
	 */
	public function text($phrase) {
		$this->phrase = trim($phrase);
		return $this;
	}

	/**
	 * Set filtering by active state
	 *
	 * @return UserFilter
	 */
	public function active() {
		$this->active = TRUE;
		return $this;
	}

	/**
	 * Set filtering by inactive state
	 *
	 * @return UserFilter
	 */
	public function inactive() {
		$this->active = FALSE;
		return $this;
	}

	/**
	 * Set filtering by arbitrary state
	 *
	 * @param string $state
	 * @return UserFilter
	 */
	public function state($state) {
		$this->state = $state;
		return $this;
	}

	/**
	 * Set ordering by user id
	 *
	 * @return UserFilter
	 */
	public function orderById($ascending = TRUE) {
		$this->orderBy = "id " . ($ascending ? "ASC" : "DESC");
		return $this;
	}

	/**
	 * Set ordering by full name
	 *
	 * @return UserFilter
	 */
	public function orderByName($ascending = TRUE) {
		$this->orderBy = "name " . ($ascending ? "ASC" : "DESC");
		return $this;
	}

	/**
	 * Return SQL where clause and query parameters hash
	 *
	 * @return array
	 */
	public function get() {
		$where = array();
		$params = array();

		if (NULL !== $this->id) {
			$where[] = "id = :id";
			$params["id"] = $this->id;
		}

		if (NULL !== $this->phrase) {
			$where[] = "(login LIKE '%:login%' OR name LIKE '%:shortName%' OR name LIKE '%:name%')";
			$params["login"] = $this->phrase;
			$params["shortName"] = $this->phrase;
			$params["name"] = $this->phrase;
		}

		if (NULL !== $this->active) {
			$where[] = $this->active ? "state = :state" : "state <> :state";
			$params["state"] = User::STATE_ACTIVE;
		} elseif (NULL !== $this->state) {
			$where[] = "state = :state";
			$params["state"] = $this->state;
		}

		$where = empty($where) ? "TRUE" : implode(" AND ", $where);
		$orderBy = $this->orderBy ? " ORDER BY {$this->orderBy}" : "";
		return array($where . $orderBy, $params);
	}

}