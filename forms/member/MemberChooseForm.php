<?php

final class MemberChooseForm extends Form {

	public function __construct($action, array $ids) {
		parent::__construct();
		$this->addElement("hidden", "action", $action);
		$this->addElement("select", "member_id", "Socio", $ids);
		$this->addElement('submit', 'btnSubmit', 'Cambiar Estado');
	}

}