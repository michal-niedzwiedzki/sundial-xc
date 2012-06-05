<?php

require_once "class.listing.php";
//require_once ("fpdf/fpdf.php");

class cDirectory extends FPDF {

	var $member_list;
	var $offer_list;
	var $want_list;

	public function cDirectory () {
		$this->member_list = new cMemberGroup();
		$this->member_list->LoadMemberGroup();
		$this->offer_list = new cListingGroup(OFFER_LISTING);
		$this->offer_list->LoadListingGroup("%");
		$this->want_list = new cListingGroup(WANT_LISTING);
		$this->want_list->LoadListingGroup("%");
	}

	public function Header() {
		global $title;
		// Arial bold 15
		$this->SetFont('Arial','B',15);
		// Calculate width of title and position
		$w = $this->GetStringWidth($title)+6;
		$this->SetX((210-$w)/2);
		// Colors of frame, background and text
		$this->SetDrawColor(0,80,180);
		$this->SetFillColor(230,230,0);
		$this->SetTextColor(220,50,50);
		// Thickness of frame (1 mm)
		$this->SetLineWidth(1);
		// Title
		$this->Cell($w,9,$title,1,1,'C',true);
		// Line break
		$this->Ln(10);
	}

	public function Footer() {
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Text color in gray
		$this->SetTextColor(128);
		// Page number
		$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
	}

	public function SectionTitle($label) {
		// Arial 12
		$this->SetFont('Arial','',12);
		// Background color
		$this->SetFillColor(200,220,255);
		// Title
		$this->Cell(0,6,"Chapter $num : $label",0,1,'L',true);
		// Line break
		$this->Ln(4);
	}

	public function SectionBody($file) {
		// Read text file
		$txt = file_get_contents($file);
		// Times 12
		$this->SetFont('Times','',12);
		// Output justified text
		$this->MultiCell(0,5,$txt);
		// Line break
		$this->Ln();
		// Mention in italics
		$this->SetFont('','I');
		$this->Cell(0,5,'(end of excerpt)');
	}

	public function PrintSectionMembers() {
		$this->AddPage();
		$this->SectionTitle("Listado de Soci@s");
		$this->SectionBodyMembers();
	}

	public function SectionBodyMembers($file) {
		// Times 12
		$this->SetFont('Times','',12);
		foreach ($this->member_list->members as $member) {
			if ($member->account_type == "F") {
				// Skip fund accounts
				continue;
			}
			$this->Cell(0,10,"",0,1);
			$this->Cell(0,10,utf8_decode($member->PrimaryName()),0,1);
			$this->Cell(0,10," (". $member->member_id .")",0,1);
			if ($member->person[0]->email) {
				$this->Cell(0,10,utf8_decode("Correo electrónico: ".$member->person[0]->email),0,1);
			}
			if ($member->person[0]->phone1_number) {
				$this->Cell(0,10,utf8_decode("Teléfono(s): ".$member->person[0]->DisplayPhone(1)),0,1);
				if ($member->person[0]->phone2_number) {
					$this->Cell(0,10,", ". $member->person[0]->DisplayPhone(2),0,1);
				}
				$this->Ln();
			}
		}
		// Line break
		$this->Ln();
	}

	public function PrintSection($num, $title, $file) {
		$this->AddPage();
		$this->ChapterTitle($num,$title);
		$this->ChapterBody($file);
	}

}

?>