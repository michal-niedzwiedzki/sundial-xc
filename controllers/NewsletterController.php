<?php

final class NewsletterController extends Controller {

	public function delete() {
		include ROOT_DIR . "/legacy/newsletter_delete.php";
	}

	public function save() {
		include ROOT_DIR . "/legacy/newsletter_save.php";
	}

	public function upload() {
		include ROOT_DIR . "/legacy/newsletter_upload.php";
	}

}