<?php

final class TradesController extends Controller {

	/**
	 * @Title "Intercambios pendientes"
	 */
	public function pending() {
		$page = $this->page;
		include ROOT_DIR . "/legacy/trades_pending.php";
	}

	/**
	 * @Title "Ver intercambios para un socio"
	 */
	public function to_view() {
		$form = new UsersListForm();
		$this->page->form = $form;
		if (!$form->validate()) {
			return;
		}
		$form->freeze();
		$form->process();
		$values = $form->exportValues();
		header("Location: " . HTTP_BASE . "/trade_history.php?mode=other&member_id=" . $values["member_id"]);
	}

}