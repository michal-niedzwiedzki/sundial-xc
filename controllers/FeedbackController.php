<?php

final class FeedbackController extends Controller {

	/**
	 * @Title "Feedback"
	 */
	public function all() {
		$mode = HTTPHelper::rq("mode");
		$memberId = $mode == "other"
			? HTTPHelper::rq("member_id")
			: cMember::getCurrent()->getId();

		$member = new cMember;
		$member->LoadMember($memberId);
		
		$feedbackgrp = new cFeedbackGroup;
		$feedbackgrp->LoadFeedbackGroup($memberId);
		
		if (isset($feedbackgrp->feedback)) {
			$this->page->table = $feedbackgrp->DisplayFeedbackTable($user->member_id);
		}
		$this->page->other = $mode == "other";
		$this->page->user = $member;
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