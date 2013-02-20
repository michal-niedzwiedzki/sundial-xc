<?php

final class FeedbackController extends Controller {

	/**
	 * @Title "Feedback"
	 */
	public function all() {
		$mode = HTTPHelper::rq("mode");
		$userId = $mode == "other"
			? HTTPHelper::rq("user_id")
			: User::getCurrentId();

		$user = User::getById($userId);

		$feedbackgrp = new cFeedbackGroup;
		$feedbackgrp->LoadFeedbackGroup($userId);

		if (isset($feedbackgrp->feedback)) {
			$this->view->table = $feedbackgrp->DisplayFeedbackTable($user->id);
		}
		$this->view->other = $mode == "other";
		$this->view->user = $member;
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