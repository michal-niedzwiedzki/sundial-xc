<?php

final class TimeframeController extends Controller {

	/**
	 * @Title "Elige intercambios de un rango de tiempo"
	 */
	public function choose() {
		$action = HTTPHelper::rq("action");
		$form = new TimeframeChooserForm($action);
		$this->page->form = $form;

		if (!$form->validate()) {
			return;
		}

		$form->freeze();
		$form->process();
		$values = $form->exportValues();

		$date = $values['from'];
		$from = urlencode($date['Y'] . '-' . $date['F'] . '-' . $date['d']);
		$date = $values['to'];
		$to = urlencode($date['Y'] . '-' . $date['F'] . '-' . $date['d']);

		header("Location:".HTTP_BASE."/{$action}.php?from={$from}&to={$to}");
	}

}