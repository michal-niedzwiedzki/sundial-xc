<?php

final class ManageController extends Controller {

	/**
	 * @Title "Gestionar restricciones de cuentas"
	 * @Level 1
	 */
	public function restrictions() {
		$config = Config::getInstance();

		$user = cMember::getCurrent();
		$user->MustBeLevel(1);

		$sql = "
			SELECT * FROM member AS m, person AS p
			WHERE m.member_id = p.member_id AND primary_member = 'Y'
			ORDER BY first_name, last_name
		";
		$members = PDOHelper::fetchAll($sql, array());

		$restrictedM = array();
		$okM = array();
		foreach ($members as $m) {
			($m["restriction"] == 1)
				? $restrictedM[] = $m
				: $okM[] = $m;
		}
		$this->view->restrictedM = $restrictedM;
		$this->view->okM = $okM;
		$this->view->csrf = CSRF;

		// leave early if not posted
		if (!HTTPHelper::rq("process")) {
			return;
		}

		if (HTTPHelper::rq("doRestrict")) {
			$id = HTTPHelper::rq("ok");
			if (!$id) {
				PageView::setMessage("No hay id de socio.");
				return;
			}
			$member = new cMember;
			$member->LoadMember($id);
			$out = PDOHelper::update("member", array("restriction" => 1), "member_id = :id", array("id" => $id));
			if (!$out) {
				PageView::setMessage("Imposible poner restricciónes sobre esta cuenta.");
				return;
			} else {
				PageView::setMessage("Restricción puesta sobre la cuenta con ID de soci@ " . e($id));
				$mailed = $member->esmail($member->person[0]->email, "Acceso restringido en ".SITE_LONG_TITLE."", $config->legacy->LEECH_EMAIL_URLOCKED);
			}
		} elseif (HTTPHelper::rq("liftRestrict")) {
			$id = HTTPHelper::rq("restricted");
			if (!$id) {
				PageView::setMessage("No hay ID de socio.");
				return;
			}
			$member = new cMember;
			$member->LoadMember($id);
			$out = PDOHelper::update("member", array("restriction" => 0), "member_id = :id", array("id" => $id));
			if (!$out) {
				PageView::setMessage("El sistema no ha podido levantar la restricción.");
				return;
			} else {
				PageView::setMessage("Restricción levantada para cuenta con ID de soci@ " . e($id));
				$mailed = $member->esmail($member->person[0]->email, "Restricción de cuenta levantada en ".SITE_LONG_TITLE."", $config->legacy->LEECH_EMAIL_URUNLOCKED);
			}
		}
	}

}