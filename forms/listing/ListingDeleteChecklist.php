<?php

final class ListingDeleteChecklist extends Checklist {

	public function __construct(array $items, $type, $mode, $memberId) {
		parent::__construct($items);
		$this->addElement('hidden', 'type', $type);
		$this->addElement('hidden', 'mode', $mode);
		$this->addElement('hidden', 'member_id', $memberId);
		$this->addElement('static', null, null);
		$this->addElement('submit', 'btnSubmit', 'Borrar');
	}

}