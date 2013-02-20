<?php

/**
 * Email notifier for new services in the system
 *
 * Generates email with services created within number or days
 * and adds recipients wishing to receive email in such intervals.
 *
 * @author Michał Rudnicki <michal.rudnicki@epsi.pl>
 */
class EmailListingUpdatesExecutor extends CronJobExecutor {

	protected $message;
	protected $interval;

	public function __construct(array $settings) {
		parent::__construct($settings);
		$this->interval = $settings["interval"];
	}

	public function execute() {
		$since = new cDateTime("-{$this->interval} DAYS");
		$listings = new cListingGroup(OFFER_LISTING);
		$listings->LoadListingGroup(NULL, NULL, NULL, $since->MySQLTime());
		$offeredText = $listings->DisplayListingGroup(TRUE);
		$listings = new cListingGroup(WANT_LISTING);
		$listings->LoadListingGroup(NULL, NULL, NULL, $since->MySQLTime());
		$wantedText = $listings->DisplayListingGroup(TRUE);

		$body = "";
		if ($offeredText != "Ninguno encontrado") {
			$body .= "<h2>Servicios Ofrecidos</h2>{$offeredText}";
		}
		if ($wantedText != "Ninguno encontrado") {
			$body .= "<h2>Servicios Solicitados</h2>{$wantedText}";
		}
		if (!$body) {
			return;
		}

		$body = "<html><body><p>" . LISTING_UPDATES_MESSAGE . "</p>{$body}</body></html>";

		switch ($this->interval) {
			case 1: $period = "de las últimas 24 horas"; break;
			case 7: $period = "de la última semana"; break;
			default: $period = "del último mes";
		}

		$this->message = new EmailMessage(EMAIL_ADMIN, SITE_SHORT_TITLE .": Listados nuevos o actualizados ". $period, $body);
		$this->message->toAll(User::getAllActive());
		$this->message->save();
	}

	public function getMessage() {
		return $this->message;
	}

}