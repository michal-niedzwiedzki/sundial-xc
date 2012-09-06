<?php

class UsersListForm extends Form {

	public function __construct(array $usersList = array()) {
		parent::__construct();
		if (empty($usersList)) {
			$g = new cMemberGroup;
			$g->LoadMemberGroup();
			$usersList = $g->MakeIDArray();
		}
		$this->addElement("select", "member_id", "Elige el socio", $usersList);
		$this->addElement("submit", "btnSubmit", "Ver");
	}

}