<?php

require_once ROOT_DIR . "/external/pear/HTML/QuickForm.php";

abstract class Form extends HTML_QuickForm {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::HTML_QuickForm();
		$this->addElement("hidden", "csrf", CSRF);
		$this->setRequiredNote("<tr><td colspan=\"2\"><span class=\"required\">*</span> &ndash; campo obligatorio</td></tr>");
		$renderer = $this->defaultRenderer();
		$renderer->setFormTemplate("<form{attributes}><table>{content}</table></form>");
		$renderer->setHeaderTemplate("<tr><th colspan=\"2\">{header}</th></tr>");
		$renderer->setElementTemplate("<tr><td class=\"label-column\">{label}<!-- BEGIN required --> <span class=\"required\">*</span><!-- END required --></td><td class=\"field-column\">{element}<!-- BEGIN error --><span class=\"error\">{error}</span><!-- END error --></td></tr>");
	}

	/**
	 * Validate form against CSRF and custom rules
	 *
	 * @see HTML_QuickForm::validate()
	 * @return boolean
	 */
	public function validate() {
		if (CSRF != $this->getElementValue("csrf")) {
			return FALSE;
		}
		return parent::validate();
	}

	/**
	 * Process form and return field values
	 *
	 * @see HTML_QuickForm::process()
	 * @return array
	 */
	public function process($whatever1 = NULL, $whatever2 = NULL) {
		parent::process(array("Form", "nop"), TRUE);
		return $this->exportValues();
	}

	/**
	 * Dummy callback function
	 */
	public static function nop() {
		return TRUE;
	}

	/**
	 * Render form as HTML string
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->toHTML();
	}

}