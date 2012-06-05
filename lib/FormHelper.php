<?php

require_once ROOT_DIR . "/external/pear/HTML/QuickForm.php";

final class FormHelper {

	public static function standard() {
		$form = new HTML_QuickForm();
		$form->setRequiredNote("<tr><td colspan=\"2\"><span class=\"required\">*</span> &ndash; campo obligatorio</td></tr>");
		$renderer = $form->defaultRenderer();
		$renderer->setFormTemplate("<form{attributes}><table>{content}</table></form>");
		$renderer->setHeaderTemplate("<tr><th colspan=\"2\">{header}</th></tr>");
		$renderer->setElementTemplate("<tr><td class=\"label-column\">{label}<!-- BEGIN required --> <span class=\"required\">*</span><!-- END required --></td><td class=\"field-column\">{element}<!-- BEGIN error --><span class=\"error\">{error}</span><!-- END error --></td></tr>");
		return $form;
	}

	public static function checklist() {
		$form = new HTML_QuickForm();
		$form->setRequiredNote("<tr><td colspan=\"2\"><span class=\"required\">*</span> &ndash; campo obligatorio</td></tr>");
		$renderer = $form->defaultRenderer();
		$renderer->setFormTemplate("<form{attributes}><table>{content}</table></form>");
		$renderer->setHeaderTemplate("<tr><th>{header}</th></tr>");
		$renderer->setElementTemplate("<tr><td>{element}<!-- BEGIN required --> <span class=\"required\"><!-- END required --><!-- BEGIN error --><span class=\"error\">{error}</span><!-- END error -->&nbsp;{label}</TD></TR>");
		return $form;
	}

	public static function verifyMax255(HTML_QuickForm $form) {
		$form->registerRule('verify_max255', 'function', 'verify_max255');
	}

	public static function verifySelection(HTML_QuickForm $form) {
		$form->registerRule('verify_selection', 'function', 'verify_selection');
	}

}

function verify_max255($z, $text) {
	return strlen($text) <= 255;
}

function verify_selection($z, $selection) {
	return $selection != "0";
}
