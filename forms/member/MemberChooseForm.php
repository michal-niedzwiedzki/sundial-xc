<?php

final class MemberChooseForm extends Form {

	public function __construct($action, array $ids) {
		parent::__construct();
		$this->addElement("hidden", "action", $action);
		$this->addElement("select", "member_id", _("(field)User name"), $ids);
		$this->addElement("submit", "btnSubmit", _("(button)Select"));
	}

}