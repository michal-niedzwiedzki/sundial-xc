<?php

if (ALLOW_IMAGES!=true) 
	header("location:http://".HTTP_BASE."/");
	
if(!extension_loaded('gd')) {
	cError::getInstance()->Error("The GD extension is required for photo uploads!");
	include("redirect.php");
}

$p->site_section = EVENTS;
$user = cMember::getCurrent();

$member = new cMember;

if($_REQUEST["mode"] == "admin") {
  $user->MustBeLevel(1);
  
	$member->LoadMember($_REQUEST["member_id"]);

	$p->page_title = "Subir/cambiar foto para un soci@";
} else {
	cMember::getCurrent()->MustBeLoggedOn();
	$member = $user;
	$p->page_title = "Subir una foto";

}

#$tableName = DB::UPLOADS;
#$sql = "SELECT filename FROM $tableName WHERE title = :$title";
#$row = PDOHelper::fetchRow($sql, array("title" => "mphoto_" . $member->member_id));
		
$mIMG = cMember::DisplayMemberImg($member->member_id);

$form = FormHelper::standard();

if ($mIMG!=false) {
			
		$form->addElement("html", $mIMG."<p>");
		$submitTxt = 'Cambiar imagen';		
}
else
	$submitTxt = 'Subir imagen';
		
$form->addElement('hidden', 'member_id', $member->member_id);
$form->addElement('hidden', 'mode', $_REQUEST["mode"]);

$form->addElement('file', 'userfile', 'Seleccionar archivo para subir:', array("MAX_FILE_SIZE"=>MAX_FILE_UPLOAD));
$form->addElement("select", "publicar", "¿Publicar una foto en el perfil de soci@?", array("0"=>"No", "1"=>"Si"));
$form->addElement('file', 'profilefile', 'Seleccionar archivo para subir en perfil:', array("MAX_FILE_SIZE"=>MAX_FILE_UPLOAD));
$form->addElement("static", null, null, null);	
$form->addElement('submit', 'btnSubmit', $submitTxt);

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process('process_data', false);
} else {
   $p->DisplayPage($form->toHtml());  // just display the form
}

function resize_image($filepath) {
  $image = Image_Transform::factory("GD"); // Need to shrink photo
  $image->load($filepath);
		
  $x = $image->getImageWidth();
  $y = $image->getImageHeight();
  
  if ($x>=MEMBER_PHOTO_WIDTH || UPSCALE_SMALL_MEMBER_PHOTO==true) {
			
    $y = @(MEMBER_PHOTO_WIDTH/$x) * $y; // Keep proportions
    $x = MEMBER_PHOTO_WIDTH;
    
    $image->resize($x,$y); 
  }
		
  if($image->save($filepath, null, 100))
    return "Archivo nuevo subido.";
  else
    return "Ha ocurrido un problema cambiando el tamaño del archivo.";

}

function SaveUploadProfile($filename) {
		// Copy file uploaded by UploadForm class to profile uploads directory but don't save entry in database 
		global $cDB;
			
		if(move_uploaded_file($_FILES['profilefile']['tmp_name'], PROFILE_UPLOADS_PATH . $filename)) {
		  return true;
			}				
		 else {
			cError::getInstance()->Error("No ha sido posible guardar el archivo, posiblemente por un problema de permisos o por el tamaño del archivo. El tamaño maximo permitido es de ".MAX_FILE_UPLOAD." bytes.");
			return false;
		}
	}

function UpdatePublishValue($publish, $title) {
  global $cDB;
 
  //echo "UPDATE ".DB::UPLOADS." SET note=" .$cDB->EscTxt($publish) . " WHERE title =" .$cDB->EscTxt($title).";";
  
  $success = $cDB->Query("UPDATE ".DB::UPLOADS." SET note=" .$cDB->EscTxt($publish) . " WHERE title =" .$cDB->EscTxt($title).";");
  
	}

function process_data ($values) {
	global $p, $member,$cDB,$cErr;

	$publish = "";
	$loadmain = false;
	$loadprofile = false;

	if ($values['publicar']=="0")
	  $publish = "no publicar";
        else
          $publish = "NULL";

	if ($_FILES['userfile']['size']==0) {
	  if ($_FILES['profilefile']['size']==0) {
			cError::getInstance()->Error("El tamaño del archivo es de 0 bytes.");
			$output = 'El tamaño del archivo es de 0 bytes.';
			$p->DisplayPage($output);
			exit;
			}
	  else {
	    $loadprofile = true;
	    	}
	}
	else {
	    $loadmain = true;
	    if ($_FILES['profilefile']['size']!==0) {
	      $loadprofile = true;
	    	}
	}
	$name = "mphoto_".$member->member_id;
	$nameprofile = "mphoto_".$member->member_id ."_profile";
	$copy = false;
	
	$query = $cDB->Query("SELECT upload_date, type, title, filename, note FROM ".DB::UPLOADS." WHERE title=".$cDB->EscTxt($name)." limit 0,1;");
	
	if ($query)
		$num_results = mysql_num_rows($query);

	if(($num_results>0)) { // Member already has a pic		
	  if ($loadmain) {
		$row = mysql_fetch_array($query);
		
		$fileLoc = UPLOADS_PATH . stripslashes($row["filename"]);
		
		@unlink($fileLoc);
		
		$query = "DELETE FROM ". DB::UPLOADS ." WHERE filename = ". $cDB->EscTxt($row["filename"]) .";";
	
		$delete = $cDB->Query($query);
		}
	  else {
	    UpdatePublishValue($publish, $name);
	    	}
	}
	// if there is no existing main file and only the profile file has been chosen then make this the main file too 
	else {
	  if (($loadprofile) && (!$loadmain)) {
	    $copy = true;
	    $loadprofile = false;
	    $loadmain = true;
	    $_FILES['userfile']['size']= $_FILES['profilefile']['size'];
	    $_FILES['userfile']['name']= $_FILES['profilefile']['name'];
	    $_FILES['userfile']['tmp_name']= $_FILES['profilefile']['tmp_name'];
	  }	
	}

	$upload = new cUpload("P", $name, $publish, $name);
	
	if(($loadmain) && ($upload->SaveUpload(true))) {
	  $output = resize_image(UPLOADS_PATH .$upload->filename);
	  if ($copy) 
	    copy(UPLOADS_PATH . $upload->filename, PROFILE_UPLOADS_PATH . $nameprofile);
	} else {
		$output = "Ha ocurrido un problema subiendo el archivo principal.";
		}

	if($loadprofile) {
	  if (SaveUploadProfile($nameprofile))
	    $output = resize_image(PROFILE_UPLOADS_PATH .$nameprofile);
	 else 
		$output = "Ha ocurrido un problema subiendo el archivo del perfil.";
	}	

	$p->DisplayPage($output);
}

?>