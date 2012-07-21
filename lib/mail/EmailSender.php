<?php

/**
 * Email sender
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class EmailSender {

	/**
	 * Send email message to recipients
	 *
	 * Sequentially sends email to recipients not yet processed.
	 * Upon successful completion marks the message as processed.
	 * Returns TRUE if all emails were sent successfuly, FALSE otherwise.
	 *
	 * @param EmailMessage $message
	 * @return boolean
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public static function send(EmailMessage $message) {
		if ($message->isProcessed()) {
			throw new EmailException("Message is already processed and cannot be sent again");
		}

		// prepare email details
		$headers = implode("\r\n", array(
			"From: {$message->getFrom()}",
			"MIME-Version: 1.0",
			"Content-type: text/plain; charset=utf-8",
			"Content-Transfer-Encoding: 8bit"
		));
		$subject = "=?utf-8?B?" . base64_encode($message->getSubject()) . "?=";
		$body = htmlspecialchars_decode($message->body, ENT_QUOTES, "UTF-8"); // FIXME: what is this for?

		// send to all recipients
		$allOk = TRUE;
		foreach ($message->getRecipients() as $address => $recipient) {
			if ($recipient["status"] !== EmailMessage::STATUS_PENDING) {
				continue;
			}
			$out = LIVE
				? mail($address, $subject, $body, $headers);
				: TRUE;
			$out or $allOk = FALSE;
			$message->updateRecipientStatus($address, $out ? EmailMessage::STATUS_SENT : EmailMessage::STATUS_ERROR);
		}

		// mark as processed if successfully sent and update database
		$allOk and $message->markAsProcessed();
		$message->save();
		return $allOk;
	}

}