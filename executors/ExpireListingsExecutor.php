<?php

/**
 * Expire listing of inactive users and notify by email
 *
 * Checks for users who remained inactive for certain time and expires their offers.
 * Emails expiry notification to users.
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class ExpireListingsExecutor extends CronJobExecutor {

	protected $message;

	public function execute() {
		$config = Config::getInstance();
		$maxDaysInactive = $config->legacy->MAX_DAYS_INACTIVE;
		$expirationWindow = $config->legacy->EXPIRATION_WINDOW;
		$deleteExpiredAfter = $config->legacy->DELETE_EXPIRED_AFTER;
		$shortTitle = $config->site->short;
		$body = "Hello,\n\nDue to inactivity, your {$shortTitle} listings have been set to automatically expire {$expirationWindow} days from now.\n\nIn order to keep the {$shortTitle} system up to date and working smoothly for all members, we have developed an automatic system to expire listings for members who haven't recorded exchanges or updated their listings during a period of {$maxInactiveDays} days. We want the directory to be up to date, so that members do not encounter listings that are out of date or expired. This works to everyone's advantage.\n\nWe apologize for any inconvenience this may cause you and thank you for your participation. You have {$expirationWindow} days to login to the system and reactivate listings that you would still like to have in the directory.  If you do not reactivate them during that timeframe, your listings will no longer appear in the directory, but will still be stored in the system for another {$deleteExpiredAfter} days, during which time you can still edit and reactivate them.";

		$this->message = new EmailMessage("Información importante sobre tu cuenta con {$shortTitle}", $body);

		// iterate over all users
		$group = new cMemberGroup();
		$group->LoadMemberGroup();
		foreach ($group->getMembers() as $member) {
			// check if expired
#echo "Days since last trade {$member->DaysSinceLastTrade()} < $maxDaysInactive\n";
#echo "Days since updated listing {$member->DaysSinceUpdatedListing()} < $maxDaysInactive\n";
			if ($member->DaysSinceLastTrade() < $maxDaysInactive or $member->DaysSinceUpdatedListing() < $maxDaysInactive) {
				continue;
			}

			// get listings
			$offered = new cListingGroup(OFFER_LISTING);
			$wanted = new cListingGroup(WANT_LISTING);
			$offeredExist = $offered->LoadListingGroup(NULL, NULL, $member->member_id, NULL, FALSE);
			$wantedExist = $wanted->LoadListingGroup(NULL, NULL, $member->member_id, NULL, FALSE);

			// expire listings and notify by email
			if ($offeredExist or $wantedExist)	{
				$expireDate = new cDateTime("+ {$expirationWindow} days");
				$offeredExist and $offered->ExpireAll($expireDate);
				$wantedExist and $wanted->ExpireAll($expireDate);
				$this->message->to($member);
			}
		}
	}

	public function getMessage() {
		return $this->message;
	}

}