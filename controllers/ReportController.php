<?php

final class ReportController extends Controller {

	/**
	 * @Title "Socios que nunca han entrado en la aplicación"
	 * @Level 1
	 */
	public function no_login() {
		$table = new cTable;
		$table->AddSimpleHeader(array("Socio", "Fecha de inscripción", "Número(s) de teléfono", "Correo electronico"));
		// $table->SetFieldAlignRight(4);  // row 4 is numeric and should align to the right
		
		$allmembers = new cMemberGroup;
		$allmembers->LoadMemberGroup();
		
		foreach ($allmembers->members as $member) {
			if ($member->account_type == "F")  // Skip fund accounts
				continue;

			$history = new cLoginHistory;
			if (!$history->LoadLoginHistory($member->member_id)) { // Have they logged in?
				$join_date = new cDateTime($member->join_date);
				$data = array($member->PrimaryName(), $join_date->ShortDate(), $member->AllPhones(), $member->AllEmails());
				$table->AddSimpleRow($data);
			}
		}
		$this->page->table = $table;

		if (!$table->DisplayTable()) {
			$this->page->message = "Todos los socios han entrado al menos una vez en la aplicación.";
		}
	}

}