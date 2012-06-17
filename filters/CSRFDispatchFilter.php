<?php

/**
 * Dispatch filter checking CSRF token
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class CSRFDispatchFilter implements DispatchFilter {

	public function before() {
		if (!isset($_SERVER["REQUEST_METHOD"]) or $_SERVER["REQUEST_METHOD"] !== "POST") {
			return;
		}
		if (!isset($_POST["csrf"]) or $_POST["csrf"] === "") {
			Debug::log("CSRF token missing", Debug::WARNING);
		} elseif ($_SESSION["csrf"] != $_POST["csrf"]) {
			Debug::log("CSRF token invalid (is {$_POST['csrf']}, should be {$_SESSION['csrf']})", Debug::WARNING);
		}
	}

	public function after() { }

}