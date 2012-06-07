<?php

/**
 * Controller dispatcher
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class Dispatcher {

	protected $controllerName;
	protected $actionName;

	protected $controllers = array();
	protected $filters = array();

	protected static $instance;

	/**
	 * Singleton constructor
	 */
	protected function __construct() { }

	/**
	 * Return singleton instance
	 *
	 * @return Dispatcher
	 */
	public static function getInstance() {
		self::$instance or self::$instance = new Dispatcher();
		return self::$instance;
	}

	/**
	 * Add filter in dispatch flow
	 *
	 * @param DispatchFilter $f
	 * @return $this
	 */
	public function addFilter(DispatchFilter $f) {
		$this->filters[] = $f;
		return $this;
	}

	/**
	 * Apply filters before dispatch
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function preFilter() {
		for ($i = 0, $j = count($this->filters); $i < $j; ++$i) {
			$this->filters[$i]->before();
		}
	}

	/**
	 * Apply filters after dispatch
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function postFilter() {
		for ($i = count($this->filters); $i > 0; --$i) {
			$this->filters[$i - 1]->after();
		}
	}

	/**
	 * Dispatch request and hand it over to controller
	 *
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function dispatch() {
		$c = $this->getController();
		$a = $this->actionName;
		$c->$a();
	}

	/**
	 * Configure controller and action name for dispatch
	 *
	 * @param string $controllerName
	 * @param string $actionName
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function configure($controllerName, $actionName) {
		$this->controllerName = $controllerName;
		$this->actionName = $actionName;
	}

	/**
	 * Return controller instance
	 *
	 * @return Controller
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function getController() {
		if (isset($this->controllers[$this->controllerName])) {
			return $this->controllers[$this->controllerName];
		}
		$pathname = $this->getPathname();
		if (!file_exists($pathname)) {
			$this->controllerName = "error";
			$this->actionName = "notFound";
			$pathname = $this->getPathname();
		}
		require_once $pathname;
		$className = $this->getControllerClass();
		$this->controllers[$this->controllerName] = new $className();
		return $this->controllers[$this->controllerName];
	}

	/**
	 * Return pathname to controller
	 */
	public function getPathname() {
		return ROOT_DIR . "/controllers/" . ucfirst($this->controllerName) . "Controller.php";
	}

	public function getControllerClass() {
		return ucfirst($this->controllerName) . "Controller";
	}

	public function getControllerName() {
		return $this->controllerName;
	}

	public function getActionName() {
		return $this->actionName;
	}

	public function getAnnotation($annotation) {
		static $reflectors = array();
		static $annotations = array();

		// return cached annotation if present
		$key = "{$this->controllerName}.{$this->actionName}";
		if (isset($annotations[$key][$annotation])) {
			return $annotations[$key][$annotation];
		}

		// get controller and initialize cache
		$c = $this->getController();
		isset($reflectors[$key]) or $reflectors[$key] = new ReflectionMethod($c, $this->actionName);
		isset($annotations[$key]) or $annotations[$key] = array();

		// fetch annotation
		$annotations[$key][$annotation] = AnnotationParser::get($reflectors[$key], $annotation);
		return $annotations[$key][$annotation];
	}

}