<?php

abstract class Checklist extends Form {
	
	public function __construct(array $items = array()) {
		parent::HTML_QuickForm();
		$this->setRequiredNote("<tr><td colspan=\"2\"><span class=\"required\">*</span> &ndash; campo obligatorio</td></tr>");
		$renderer = $this->defaultRenderer();
		$renderer->setFormTemplate("<form{attributes}><table>{content}</table></form>");
		$renderer->setHeaderTemplate("<tr><th>{header}</th></tr>");
		$renderer->setElementTemplate("<tr><td>{element}<!-- BEGIN required --> <span class=\"required\"><!-- END required --><!-- BEGIN error --><span class=\"error\">{error}</span><!-- END error -->&nbsp;{label}</TD></TR>");
		empty($items) or $this->addItems($items);
	}

	public function addItems(array $items) {
		foreach ($items as $id => $title) {
			$title and $this->addElement('checkbox', $id, $title);
		}
	}

}