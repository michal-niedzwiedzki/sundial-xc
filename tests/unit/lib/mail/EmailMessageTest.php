<?php

require_once dirname(__FILE__) . "/../../../../bootstrap.php";
require_once "PHPUnit/Autoload.php";

/**
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
final class EmailMessageTest extends PHPUnit_Framework_TestCase {

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testContruct() {
		$message = new EmailMessage("test@test.com", "Subject", "Body");
		$this->assertEquals("test@test.com", $message->getFrom());
		$this->assertEquals("Subject", $message->getSubject());
		$this->assertEquals("Body", $message->getBody());
	}

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testAddRecipientGetRecipients() {
		$message = new EmailMessage("test@test.com", "Subject", "Body");
		$message->addRecipient(EmailMessage::FIELD_TO, 1, "Recipient1", "recipient1@test.com", NULL, EmailMessage::STATUS_PENDING);
		$message->addRecipient(EmailMessage::FIELD_CC, 2, "Recipient2", "recipient2@test.com", NULL, EmailMessage::STATUS_SENT);

		// test getting recipients
		$recipients = $message->getRecipients();
		$this->assertEquals(2, count($recipients));
		$expectedRecipient1 = array(
			"id" => NULL,
			"user_id" => 1,
			"field" => EmailMessage::FIELD_TO,
			"name" => "Recipient1",
			"address" => "recipient1@test.com",
			"status" => EmailMessage::STATUS_PENDING
		);
		$expectedRecipient2 = array(
			"id" => NULL,
			"user_id" => 2,
			"field" => EmailMessage::FIELD_CC,
			"name" => "Recipient2",
			"address" => "recipient2@test.com",
			"status" => EmailMessage::STATUS_SENT
		);
		$this->assertEquals($expectedRecipient1, $recipients["recipient1@test.com"]);
		$this->assertEquals($expectedRecipient2, $recipients["recipient2@test.com"]);

		// test getting recipients by status
		$pendingRecipients = $message->getRecipients(EmailMessage::STATUS_PENDING);
		$sentRecipients = $message->getRecipients(EmailMessage::STATUS_SENT);
		$this->assertEquals(1, count($pendingRecipients));
		$this->assertTrue(array_key_exists("recipient1@test.com", $pendingRecipients));
		$this->assertEquals(1, count($sentRecipients));
		$this->assertTrue(array_key_exists("recipient2@test.com", $sentRecipients));
	}

	/**
	 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
	 */
	public function testSaveAndGetById() {
		$message = new EmailMessage("test@test.com", "Subject", "Body");
		$message->addRecipient(EmailMessage::FIELD_TO, 1, "Recipient1", "recipient1@test.com", NULL, EmailMessage::STATUS_PENDING);
		$message->addRecipient(EmailMessage::FIELD_CC, 2, "Recipient2", "recipient2@test.com", NULL, EmailMessage::STATUS_SENT);

		// test saving message into database
		$message->save();
		$id = $message->getId();
		$this->assertNotNull($id);

		// test getting message from database
		$savedMessage = EmailMessage::getById($id);
		$this->assertEquals($message->getId(), $savedMessage->getId());
		$this->assertEquals($message->getRecipients(), $savedMessage->getRecipients());
	}

}