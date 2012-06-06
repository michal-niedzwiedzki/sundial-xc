<?php

/**
 * View to be rendered using given template
 *
 * All assigned properties of type string are automatically escaped.
 * Other properties are not escaped automatically.
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class View {

	protected $templateVars = array();
	protected $templateFile;

	public function __construct($templateFile) {
		$this->templateFile = ROOT_DIR . "/templates/" . $templateFile;
	}

	public function __set($var, $value) {
		is_string($value)
			? $this->templateVars[$var] = htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8")
			: $this->templateVars[$var] = $value;
	}

	public function displayPage($content = NULL) {
		$content && $this->templateVars["content"] = $content;
	}

	public function __toString() {
		ob_start();
		if (file_exists($this->templateFile)) {
			extract($this->templateVars);
			include $this->templateFile;
		} else {
			$templateFile = htmlspecialchars($this->templateFile, ENT_QUOTES, "UTF-8");
			$templateVars = $this->templateVars;
			include ROOT_DIR . "/templates/_missing.phtml";
		}
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

}