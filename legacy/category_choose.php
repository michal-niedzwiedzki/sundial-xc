<?php

require_once "../bootstrap.php";

$master = PageView::getInstance();
$master->title = "Elegir una categoría";

$form = FormHelper::standard();

//
// Define form elements
//
//$user->MustBeLevel(2);

$categories = new cCategoryList;
$category_list = $categories->MakeCategoryArray();
unset($category_list[0]);

$form->addElement("select", "category", "Categoría:", $category_list);

$buttons[] = &HTML_QuickForm::createElement('submit', 'btnEdit', 'Editar');
$buttons[] = &HTML_QuickForm::createElement('submit', 'btnDelete', 'Borrar');
$form->addGroup($buttons, null, null, '&nbsp;');

//
// Define form rules
//


//
// Then check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
	$form->freeze();
	$form->process("process_data", false);
} else {
	$master->displayPage($form->toHtml());
}

function process_data ($values) {
	if(isset($values["btnDelete"])) {
		$category = new cCategory;
		$category->LoadCategory($values["category"]);
		if($category->HasListings()) {
			$output = "Esta categoría tiene servicios asociados. Hay que mover estos servicios a otras categorias o borrarlos antes de borrar la categoría. Es posible que han sido temporalmente desactivados, y en este caso no aparecen en los listados.<P>";

			$output .= "Servicios definidos en esta categoría:<BR>";
			$listings = new cListingGroup(OFFER_LISTING);
			$listings->LoadListingGroup(null, $values["category"]);
			foreach($listings->listing as $listing)
				$output .= "OFFERED: ". $listing->description ." (". $listing->member_id .")<BR>";

			$listings = new cListingGroup(WANT_LISTING);
			$listings->LoadListingGroup(null, $values["category"]);
			foreach($listings->listing as $listing)
				$output .= "WANTED: ". $listing->description ." (". $listing->member_id .")<BR>";
		} else {
			if($category->DeleteCategory())
				$output = "La categoría ha sido borrada.";
		}
	} else {
		header("Location: ".HTTP_BASE."/category_edit.php?category_id=". $values["category"]);
		exit;
	}
	PageView::getInstance()->displayPage($output);
}
