<?php

/**
 * Master HTML view
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class PageView extends View {

	private static $instance;
	private $enabled = TRUE;

	/**
	 * Singleton constructor
	 */
	public function __construct() {
		parent::__construct("master");
		self::$instance = $this;

		$config = Config::getInstance();
		$this->title = "";
		$this->short = $config->site->short;
		$this->tagline = $config->site->tagline;
		$this->version = $config->site->version;
		$this->keywords = $config->site->keywords;
		$this->description = $config->site->title;
		$this->adminEmail = $config->admin->email;
		$this->adminPhone = $config->admin->phone;
		$this->message = "";
		$this->content = "";
		$this->debug = array();

		$sidebar = array(
			array("text" => "Inicio", "link" => "index.php"),
			array("text" => "Quiero saber más", "link" => "info/more.php"),
			array("text" => "Noticias", "link" => "news.php"),
			array("text" => "Blog", "link" => "http://a2manos-dosdemayo.blogspot.com"),
			array("text" => "Servicios ofrecidos", "link" => "listings.php?type=Offer"),
			array("text" => "Servicios solicitados", "link" => "listings.php?type=Want"),
			array("text" => "Actualizar servicios", "link" => "listings_menu.php"),
			array("text" => "Intercambios", "link" => "exchange_menu.php"),
			array("text" => "Listado de soci@s", "link" => "member_directory.php"),
			array("text" => "Perfil de soci@", "link" => "member_profile.php"),
			array("text" => "Contacto", "link" => "contact.php"),
		);
		if (cMember::getCurrent()->member_role > 0) {
			$sidebar[] = array("text" => "Administración", "link" => "admin_menu.php");
		}
		if (cMember::isLoggedOn()) {
			$sidebar[] = array("text" => "Salir", "link" => "login_logout.php");
		} else {
			$sidebar[] = array("text" => "Entrar", "link" => "member_login.php");
		}
		$this->sidebar = $sidebar;
		$this->showSidebar = TRUE;
	}

	/**
	 * Display error message
	 *
	 * @param string $message
	 */
	public function displayError($message) {
		$view = new View("error");
		$view->message = $message;
		return $this->displayPage($view);
	}

	/**
	 * Set message to be shown above page content
	 *
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * Set debugging log to be shown beneath entire page content
	 *
	 * @param array $log
	 */
	public function setDebug(array $log) {
		$this->log = $log;
	}

	/**
	 * Disable rendering page view
	 */
	public function disable() {
		$this->enabled = FALSE;
	}

	/**
	 * Return if rendering page view is enabled
	 *
	 * @return boolean
	 */
	public function isEnabled() {
		return $this->enabled;
	}

	/**
	 * Return singleton instance
	 *
	 * @return PageView
	 */
	public static function getInstance() {
		self::$instance or new PageView();
		return self::$instance;
	}

}