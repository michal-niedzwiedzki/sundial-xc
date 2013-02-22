<?php

class cUpload {
	var $upload_id;
	var $upload_date;
	var $type; // for example "N" for "newsletters"
	var $title;
	var $filename;
	var $note;

	function cUpload ($type=null, $title=null, $note=null, $filename=null) {
		$user = cMember::getCurrent();

		if($type) {
			$this->type = $type;
			$this->title = $title;
			$this->note = $note;
			$this->filename = $filename; // For the sake of being thorough [chris]
		
		}
	}
	
	function SaveUpload() {
		// Copy file uploaded by UploadForm class to uploads directory and
		// save entry for it in the database
		global $cDB;
		
		if($this->filename == null)
			$this->filename = $_FILES['userfile']['name'];

		
		
		$query = $cDB->Query("SELECT null from ". DB::UPLOADS ." WHERE filename ='".$_FILES['userfile']['name']."';");
		
		if($row = mysql_fetch_array($query)) {
			PageView::getInstance()->displayError("A file with this name already exists on the server.");
			return false;
		}		
			
		if(move_uploaded_file($_FILES['userfile']['tmp_name'], UPLOADS_PATH . $this->filename)) {
			$insert = $cDB->Query("INSERT INTO ". DB::UPLOADS ." (type, title, filename, note) VALUES (". $cDB->EscTxt($this->type) .", ". $cDB->EscTxt($this->title) .", ". $cDB->EscTxt($this->filename) .", ". $cDB->EscTxt($this->note) .");");
			//if (!$cDB->EscTxt($this->note) == "no publicar") 
			  //copy(UPLOADS_PATH . $this->filename, '/uploads/' .$this->filename);
			  //move_uploaded_file($_FILES['userfile']['tmp_name'], '/uploads/' . $this->filename)

			if(mysql_affected_rows() == 1) {
				$this->upload_id = mysql_insert_id();	
				$query = $cDB->Query("SELECT upload_date FROM ".DB::UPLOADS." WHERE  upload_id=". $this->upload_id.";");
				if($row = mysql_fetch_array($query))
					$this->upload_date = $row[0];					
				return true;
			} else {
				PageView::getInstance()->displayError("Could not save database row for uploaded file.");
				return false;
			}				
		} else {
			PageView::getInstance()->displayError("Could not save uploaded file. This could be because of a permissions problem.  Does the web user have permission to write to the uploads directory?  It could also be that the file is too large.  The current maximum size of file allowed is ".MAX_FILE_UPLOAD." bytes.");
			return false;
		}
	}

	public function LoadUpload ($uploadId) {
		$tableName = DB::UPLOADS;
		$sql = "SELECT * FROM $tableName WHERE upload_id = :id";
		$row = PDOHelper::fetchRow($sql, array("id" => $uploadId));
		if (empty($row)) {
			PageView::getInstance()->displayError("There was an error accessing the uploads table. Please try again later.");
			return;
		}
		$this->upload_id = $uploadId;
		$this->upload_date = new cDateTime($row["upload_date"]);
		$this->type = $row["type"];
		$this->title = $row["title"];
		$this->filename = $row["filename"];
		$this->note = $row["note"];
		return true;
	}

	function DeleteUpload () {
		global $cDB;
		
		if(unlink(UPLOADS_PATH . $this->filename)) {
			$delete = $cDB->Query("DELETE FROM ". DB::UPLOADS ." WHERE upload_id = ". $this->upload_id .";");
			if(mysql_affected_rows() == 1) {
				return true;
			} else {
				PageView::getInstance()->displayError("File was deleted but could not delete row from database.  The row will have to removed manually.  Please contact your systems administrator.");
				return FALSE;
			}			
		} else {
			PageView::getInstance()->displayError("Could not delete file - ". $this->filename .".  Please try again later.");
			return FALSE;
		}
	}

	function DisplayURL ($text=null) {
		if($text == null)
			$text = $this->title;
		// RF: changed to open file in uploads in new window	
		return '<A HREF="uploads/'. $this->filename .'" target="_blank">'. $text .'</A>';
	}
}

class cUploadGroup {

	var $uploads; // will be object of class cUpload
	var $type;

	function cUploadGroup($type) {
		$this->type = $type;
	}

	public function LoadUploadGroup () {
		$tableName = DB::UPLOADS;
		$sql = "SELECT upload_id FROM $tableName WHERE type = :type ORDER BY upload_date DESC";
		$rows = PDOHelper::fetchAll($sql, array("type" => $this->type));
		foreach ($rows as $i => $row) {
			$this->uploads[$i] = new cUpload;
			$this->uploads[$i]->LoadUpload($row["upload_id"]);
		}
		return !empty($rows);
	}


}

class cUploadForm {

	function DisplayUploadForm($action, $text_fields=null) {
	
	$output = '<form enctype="multipart/form-data" action="'. $action.'" method="POST">';
	foreach($text_fields as $field)
		$output .= $field .' <input type="text" name="'. $field .'"><BR>';
		
	$output .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.MAX_FILE_UPLOAD.'">Select file to upload <input name="userfile" type="file"><input type="submit" value="Upload"></form>';
	return $output;
	}

}

?>
