<?php

final class FeedbackController extends Controller {

	public function all() {
		include ROOT_DIR . "/legacy/feedback_all.php";
	}

	public function choose_inbox() {
		include ROOT_DIR . "/legacy/feedback_choose_inbox.php";
	}

	public function choose() {
		include ROOT_DIR . "/legacy/feedback_choose.php";
	}

	public function index() {
		include ROOT_DIR . "/legacy/feedback.php";
	}

	public function rebuttal() {
		include ROOT_DIR . "/legacy/feedback_rebuttal.php";
	}

	public function reply() {
		include ROOT_DIR . "/legacy/feedback_reply.php";
	}

	public function to_view() {
		include ROOT_DIR . "/legacy/feedback_to_view.php";
	}

}