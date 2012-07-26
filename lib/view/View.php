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

	protected static $ext = ".phtml";

	public function __construct($templateFile) {
		$this->templateFile = ROOT_DIR . "/templates/" . $templateFile;
	}

	public static function getExtension() {
		return self::$ext;
	}

	public static function setExtension($ext) {
		self::$ext = $ext;
	}

	public function __get($var) {
		return isset($this->templateVars[$var]) ? $this->templateVars[$var] : NULL;
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
		$f = $this->templateFile . self::$ext;
		if (file_exists($f)) {
			extract($this->templateVars);
			include $f;
		} else {
			$templateFile = htmlspecialchars($f, ENT_QUOTES, "UTF-8");
			$templateVars = $this->templateVars;
			include ROOT_DIR . "/templates/_missing";
		}
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

}