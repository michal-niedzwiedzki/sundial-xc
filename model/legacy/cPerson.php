<?php

class cPerson {

	var $person_id;
	var $member_id;
	var $primary_member;
	var $directory_list;
	var $first_name;
	var $last_name;
	var $mid_name;
	var $dob;
	var $mother_mn;
	var $email;
	var $phone1_area;
	var $phone1_number;
	var $phone1_ext;
	var $phone2_area;
	var $phone2_number;
	var $phone2_ext;
	var $fax_area;
	var $fax_number;
	var $fax_ext;
	var $address_street1;
	var $address_street2;
	var $address_city;
	var $address_state_code;
	var $address_post_code;
	var $address_country;

	public function cPerson($values = NULL) {
		if ($values) {
			$this->member_id = $values['member_id'];
			$this->primary_member = $values['primary_member'];
			$this->directory_list = $values['directory_list'];
			$this->first_name = $values['first_name'];
			$this->last_name = $values['last_name'];
			$this->mid_name = $values['mid_name'];
			$this->dob = $values['dob'];
			$this->mother_mn = isset($values['mother_mn']) ? $values['mother_mn'] : NULL;
			$this->email = isset($values['email']) ? $values['email'] : NULL;
			$this->phone1_area = isset($values['phone1_area']) ? $values['phone1_area'] : NULL;
			$this->phone1_number = isset($values['phone1_number']) ? $values['phone1_number'] : NULL;
			$this->phone1_ext = isset($values['phone1_ext']) ? $values['phone1_ext'] : NULL;
			$this->phone2_area = isset($values['phone2_area']) ? $values['phone2_area'] : NULL;
			$this->phone2_number = isset($values['phone2_number']) ? $values['phone2_number'] : NULL;
			$this->phone2_ext = isset($values['phone2_ext']) ? $values['phone2_ext'] : NULL;
			$this->fax_area = isset($values['fax_area']) ? $values['fax_area'] : NULL;
			$this->fax_number = isset($values['fax_number']) ? $values['fax_number'] : NULL;
			$this->fax_ext = isset($values['fax_ext']) ? $values['fax_ext'] : NULL;
			$this->address_street1 = isset($values['address_street1']) ? $values['address_street1'] : NULL;
			$this->address_street2 = isset($values['address_street2']) ? $values['address_street2'] : NULL;
			$this->address_city = isset($values['address_city']) ? $values['address_city'] : NULL;
			$this->address_state_code = isset($values['address_state_code']) ? $values['address_state_code'] : NULL;
			$this->address_post_code = isset($values['address_post_code']) ? $values['address_post_code'] : NULL;
			$this->address_country = isset($values['address_country']) ? $values['address_country'] : NULL;
			$this->sex = isset($values['sex']) ? $values['sex'] : NULL;
			$this->age = isset($values['age']) ? $values['age'] : NULL;
			$this->sex = isset($values['sex']) ? $values['sex'] : NULL;
			$this->about_me = isset($values['about_me']) ? $values['about_me'] : NULL;
		}
	}

	public function SaveNewPerson() {
		$tableName = DB::PERSONS;
		$sql = "
			SELECT count(*) AS c FROM $tableName
			WHERE member_id = :id OR (first_name = :firstName AND last_name = :lastName AND mid_name = :midName AND dob = :dob)
		";
		$out = PDOHelper::fetchCell("c", $sql, array("id" => $this->member_id, "firstName" => $this->first_name, "lastName" => $this->last_name, "midName" => $this->mid_name, "dob" => $this->dob));
		if ($out) {
			cError::getInstance()->Error("No ha sido posible guardar datos para esta persona. Ya existe en la base de datos un registro con los mismos datos.");
			include "redirect.php";
			return FALSE;
		}
		return PDOHelper::insert(DB::PERSONS, array(
			"member_id" => $this->member_id,
			"primary_member" => $this->primary_member,
			"directory_list" => $this->directory_list,
			"first_name" => $this->first_name,
			"last_name" => $this->last_name,
			"mid_name" => $this->mid_name,
			"dob" => $this->dob,
			"mother_mn" => (string)$this->mother_mn,
			"email" => (string)$this->email,
			"phone1_area" => (string)$this->phone1_area,
			"phone1_number" => (string)$this->phone1_number,
			"phone1_ext" => (string)$this->phone1_ext,
			"phone2_area" => (string)$this->phone2_area,
			"phone2_number" => (string)$this->phone2_number,
			"phone2_ext" => (string)$this->phone2_ext,
			"fax_area" => (string)$this->fax_area,
			"fax_number" => (string)$this->fax_number,
			"fax_ext" => (string)$this->fax_ext,
			"address_street1" => (string)$this->address_street1,
			"address_street2" => (string)$this->address_street2,
			"address_city" => (string)$this->address_city,
			"address_state_code" => (string)$this->address_state_code,
			"address_post_code" => (string)$this->address_post_code,
			"address_country" => (string)$this->address_country,
		));
	}

	public function SavePerson() {
		$properties = array(
			"member_id" => $this->member_id,
			"primary_member" => $this->primary_member,
			"directory_list" => $this->directory_list,
			"first_name" => $this->first_name,
			"last_name" => $this->last_name,
			"mid_name" => $this->mid_name,
			"dob" => $this->dob,
			"mother_mn" => (string)$this->mother_mn,
			"email" => (string)$this->email,
			"phone1_area" => (string)$this->phone1_area,
			"phone1_number" => (string)$this->phone1_number,
			"phone1_ext" => (string)$this->phone1_ext,
			"phone2_area" => (string)$this->phone2_area,
			"phone2_number" => (string)$this->phone2_number,
			"phone2_ext" => (string)$this->phone2_ext,
			"fax_area" => (string)$this->fax_area,
			"fax_number" => (string)$this->fax_number,
			"fax_ext" => (string)$this->fax_ext,
			"address_street1" => (string)$this->address_street1,
			"address_street2" => (string)$this->address_street2,
			"address_city" => (string)$this->address_city,
			"address_state_code" => (string)$this->address_state_code,
			"address_post_code" => (string)$this->address_post_code,
			"address_country" => (string)$this->address_country,
		);
		return PDOHelper::update(DB::PERSONS, $properties, "person_id = :id", array("id" => $this->person_id));
	}

	public function LoadPerson($who) {
		$tableName = DB::PERSONS;
		$sql = "SELECT * FROM $tableName WHERE person_id = :id";
		$row = PDOHelper::fetchRow($sql, array("id" => $who));
		if (empty($row)) {
			cError::getInstance()->Error("Ha ocurrido un error accediendo a los datos de ({$who}).  Intentalo otra vez mas tarde.");
			include("redirect.php");
			return FALSE;
		}
		foreach ($row as $column => $value) {
			$this->$column = $value;
		}
		return TRUE;
	}

	function DeletePerson() {
		global $cDB;
		
		if($this->primary_member == 'Y') {
			cError::getInstance()->Error("Cannot delete primary member!");	
			return false;
		} 
		
		$delete = $cDB->Query("DELETE FROM ".DB::PERSONS." WHERE person_id=". $cDB->EscTxt($this->person_id));
		
		unset($this->person_id);
		
		if (mysql_affected_rows() == 1) {
			return true;
		} else {
			cError::getInstance()->Error("Error deleting joint member.  Please try again later.");
		}
		
	}
							
	function ShowPerson()
	{
		$output = $this->person_id . ", " . $this->member_id . ", " . $this->primary_member . ", " . $this->directory_list . ", " . $this->first_name . ", " . $this->last_name . ", " . $this->mid_name . ", " . $this->dob . ", " . $this->mother_mn . ", " . $this->email . ", " . $this->phone1_area . ", " . $this->phone1_number . ", " . $this->phone1_ext . ", " . $this->phone2_area . ", " . $this->phone2_number . ", " . $this->phone2_ext . ", " . $this->fax_area . ", " . $this->fax_number . ", " . $this->fax_ext . ", " . $this->address_street1 . ", " . $this->address_street2 . ", " . $this->address_city . ", " . $this->address_state_code . ", " . $this->address_post_code . ", " . $this->address_country;
		
		return $output;
	}

	function Name() {
		return $this->first_name . " " .$this->last_name;	
	}
			
	function DisplayPhone($type)
	{
		

		switch ($type)
		{
			case "1":
				$phone_area = $this->phone1_area;
				$phone_number = $this->phone1_number;
				$phone_ext = $this->phone1_ext;
				break;
			case "2":
				$phone_area = $this->phone2_area;
				$phone_number = $this->phone2_number;
				$phone_ext = $this->phone2_ext;
				break;
			case "fax":
				$phone_area = $this->fax_area;
				$phone_number = $this->fax_number;
				$phone_ext = $this->fax_ext;
				break;
			default:
				cError::getInstance()->Error("Phone type does not exist.");
				return "ERROR";
		}
/*		
		if($phone_number != "") {
			if($phone_area != "" and $phone_area != DEFAULT_PHONE_AREA)
				$phone = "(". $phone_area .") ";
			else
				$phone = "";
				
			$phone .= substr($phone_number,0,3) ."-". substr($phone_number,3,4);
			if($phone_ext !="")
				$phone .= " Ext. ". $phone_ext;
		} else {
			$phone = "";
		}
*/
        $phone = $phone_number;
		
		return $phone;
	}
}