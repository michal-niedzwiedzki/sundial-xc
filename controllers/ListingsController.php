<?php

final class ListingsController extends Controller {

	/**
	 * @Public
	 */
	public function index() {
		$type = HTTPHelper::rq("type");
		($type === "Offer")
			? $title = "Listado de servicios ofrecidos"
			: $title = "Listado de servicios solicitados";

		$category_list = new cCategoryList();
		$categories = $category_list->MakeCategoryArray(ACTIVE, substr($type, 0, 1));
		$categories[0] = "(Ver todas)";

		$text = "Nuevos/actualizados en ";
		$options = array(
			0 => "(Ver todos)",
			3 => "Nuevos/actualizados en últimos tres días",
			7 => "Nuevos/actualizados en última semana",
			14 => "Nuevos/actualizados en últimas dos semanas",
			30 => "Nuevos/actualizados en último mes",
			90 => "Nuevos/actualizados en últimos tres meses",
		);

		$form = FormHelper::standard();
		$form->addElement("hidden","type", $type);
		$form->addElement("select", "category", "Categoría", $categories);
		$form->addElement("select", "timeframe", "Rango de Tiempo", $options);
		Config::getInstance()->legacy->KEYWORD_SEARCH_DIR and $form->addElement("text","keyword","Palabra clave");
		$form->addElement("submit", "btnSubmit", "Continuar");
		
		if ($form->validate()) { // Form is validated so processes the data
			$form->freeze();
			header("Location: " . HTTP_BASE . "/listings_found.php?type=". urlencode($type) ."&keyword=".$_REQUEST["keyword"]."&category=".$values["category"]."&timeframe=".$_REQUEST["timeframe"]);
			exit;
		}

		// render
		PageView::getInstance()->title = $title;
		$this->page->title = $title;
		$this->page->form = $form;
	}

	/**
	 * @Title "Actualizar listados de servicios"
	 */
	public function menu() {
		cMember::getCurrent()->MustBeLoggedOn();
	}

	public function create() {
		include ROOT_DIR . "/legacy/listings_create.php";
	}

	/**
	 * @Public
	 */
	public function found() {
		$config = Config::getInstance();
		$type = HTTPHelper::rq("type");
		$categoryId = HTTPHelper::rq("category");
		$timeframe = HTTPHelper::rq("timeframe");
		$keyword = strtolower(HTTPHelper::get("keyword"));
		
		$title = ($type == "Offer") ? "Servicios ofrecidos" : "Servicios solicitados";
		$category = ($categoryId == "0") ? "%" : $categoryId;
		$since = ($timeframe == "0") ? new cDateTime(LONG_LONG_AGO) : new cDateTime("-{$timeframe} dias");

		$user = cMember::getCurrent();
		$showIds = (boolean)$user->IsLoggedOn();

		// instantiate new cOffer objects and load them
		$listings = new cListingGroup($type);
		$listings->LoadListingGroup(NULL, $category, NULL, $since->MySQLTime());

		$lID = 0;

		if ($listings->listing && $config->legacy->KEYWORD_SEARCH_DIR && strlen($keyword) > 0) { // Keyword specified
			foreach ($listings->listing as $l) { // Check ->title and ->description etc against Keyword
				$mem = $l->member;
				$pers = $l->member->person[0];
				$match = false;
				if (NULL !== strpos(strtolower($l->title), $keyword)) { // Offer title
					$match = true;
				}
				if (NULL !== strpos(strtolower(htmlentities($l->description)), $keyword)) { // Offer description
					$match = true;
				}
				if ($user->IsLoggedOn()) { // Search is only performed on these params if the user is logged in
					if (NULL !== strpos(strtolower($pers->first_name), $keyword)) { // Member First Name
						$match = true;
					}
					if (NULL !== strpos(strtolower($pers->mid_name), $keyword)) { // Member Last Name
						$match = true;
					}
					if (NULL !== strpos(strtolower($mem->member_id), $keyword)) { // Member ID
						$match = true;
					}
					if (NULL !== strpos(strtolower($pers->address_post_code), $keyword)) { // Postcode
						$match = true;
					}
				}
				if (!$match) {
					unset($listings->listing[$lID]);
				}
				$lID += 1;
			}
		}

		// render
		PageView::getInstance()->title = $title;
		$this->page->title = $title;
		$this->page->table = $listings->DisplayListingGroup($showIds);
	}

}