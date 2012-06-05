<?php

require_once "../bootstrap.php";

cMember::getCurrent()->MustBeLoggedOn();

$master = PageView::getInstance();
$master->section = LISTINGS;
$master->page_title = "Descargar Directorio de Servicios";

define('FPDF_FONTPATH', "fpdf/font");
include "fpdf/fpdf.php";
include "classes/class.directory.php";

$form = FormHelper::standard();
$form->addElement("static", null, "Aquí puedes bajar una copia en formato pdf del directorio. Si no tienes un lector para el formato, debes bajar una copia de Adobe Acrobat para leer el documento <A HREF=\"http://www.tucows.com/preview/194959.html\">aquí</A>.", null);
$form->addElement("static", null, null, null);
$form->addElement("static", null, "Es posible que no puedes leer el documento con versiones antiguas de Acrobat. En este caso puedes actualizar la versión que tienes de <A HREF=\"http://www.tucows.com/preview/194959.html\">Acrobat</A>.", null);
$form->addElement("static", null, null, null);
$form->addElement("submit", "btnSubmit", "Descargar PDF");

if ($form->validate()) {
	$form->freeze();
 	$form->process("process_data", false);
} else {
	$master->DisplayPage($form->toHtml());
}

function process_data ($values) {
	$dir = new cDirectory();
	$dir->AddPage();
	$dir->SetFont('Arial','B',15);
//$dir->SetTitle("anything");
//$dir->SetAuthor(' SITE_LONG_TITLE ');
//$pdf->PrintSectionMembers();
//$pdf->PrintChapter(2,'THE PROS AND CONS','20k_c2.txt');
//$dir->Output('test.pdf', "D");
	$dir->Output();
	die();
}