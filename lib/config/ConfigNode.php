<?php

/**
 * Configuration node
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class ConfigNode implements IteratorAggregate {

	/**
	 * Configuration nodes
	 * @var array
	 */
	private $properties = array();

	/**
	 * Import properties from another object or array
	 *
	 * @param object|array $source
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function import($source) {
		if (!is_object($source) && !is_array($source)) {
			throw new ConfigException("Import source must be object or array");
		}
		foreach ($source as $property => $value) {
			$this->$property = $value;
		}
	}

	/**
	 * Export properties as nested array
	 *
	 * @return array
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function export() {
		$out = array();
		foreach ($this->properties as $property => $value) {
			$property instanceof ConfigNode
				? $out[$property] = $value->export()
				: $out[$property] = $value;
		}
		return $out;
	}

	/**
	 * Return iterator
	 *
	 * @return Iterator
	 */
	public function getIterator() {
		return new ArrayIterator(new ArrayObject($this->properties));
	}

	/**
	 * Get configuration property
	 *
	 * @return mixed
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function __get($property) {
		if (isset($this->properties[$property])) {
			return $this->properties[$property];
		}
		return NULL;
	}

	/**
	 * Set configuration property
	 *
	 * @param string $property name
	 * @param mixed $value
	 * @return mixed
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function __set($property, $value) {
		if ($value instanceof ConfigNode) {
			$this->properties[$property] = $value;
		} elseif ($value instanceof stdClass or is_array($value)) {
			$this->properties[$property] = new ConfigNode();
			$this->properties[$property]->import($value);
		} elseif (is_scalar($value) or is_null($value) or is_bool($value)) {
			$this->properties[$property] = $value;
		} else {
			throw new ConfigException("Unsupported data type in config: " . gettype($value));
		}
		return $this->properties[$property];
	}

}