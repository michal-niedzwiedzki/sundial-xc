<?php

/**
 * Generic JSON view without template
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class JSONView extends View {

	public function __construct() { }

	public function __toString() {
		return json_encode($this->templateVars);
	}

}