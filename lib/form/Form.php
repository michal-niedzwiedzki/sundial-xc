<?php

require_once ROOT_DIR . "/external/pear/HTML/QuickForm.php";

abstract class Form extends HTML_QuickForm {

	public function __construct() {
		parent::HTML_QuickForm();
		$this->setRequiredNote("<tr><td colspan=\"2\"><span class=\"required\">*</span> &ndash; campo obligatorio</td></tr>");
		$renderer = $this->defaultRenderer();
		$renderer->setFormTemplate("<form{attributes}><table>{content}</table></form>");
		$renderer->setHeaderTemplate("<tr><th colspan=\"2\">{header}</th></tr>");
		$renderer->setElementTemplate("<tr><td class=\"label-column\">{label}<!-- BEGIN required --> <span class=\"required\">*</span><!-- END required --></td><td class=\"field-column\">{element}<!-- BEGIN error --><span class=\"error\">{error}</span><!-- END error --></td></tr>");
	}

	public static function nop() {
		return TRUE;
	}

	public function process($whatever1 = NULL, $whatever2 = NULL) {
		return parent::process(array("Form", "nop"), TRUE);
	}

}