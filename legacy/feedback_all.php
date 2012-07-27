<?php

include_once("includes/inc.global.php");

include("classes/class.feedback.php");

cMember::getCurrent()->MustBeLoggedOn();
	
if($_REQUEST["mode"] == "other") 
	$member_id = $_REQUEST["member_id"];
else
	$member_id = $user->member_id;
	
$p->site_section = SECTION_FEEDBACK;
$member = new cMember;
$member->LoadMember($member_id);
$p->page_title = "Feedback for ". $member->PrimaryName();

$feedbackgrp = new cFeedbackGroup;
$feedbackgrp->LoadFeedbackGroup($member_id);

if (isset($feedbackgrp->feedback)) {
	$output = $feedbackgrp->DisplayFeedbackTable($user->member_id);
} else  {
	if($_REQUEST["mode"] == "self")
		$output = "You don't have any feedback yet.";
	else
		$output = "This member does not have any feedback yet.";
}

echo $p;
