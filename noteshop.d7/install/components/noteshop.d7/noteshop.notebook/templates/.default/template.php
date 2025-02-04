<?
//print_r("Notebook template");
//print_r($arResult);
//print_r($arParams);

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
\Bitrix\Main\UI\Extension::load("ui.bootstrap4");

$APPLICATION->SetTitle($arResult["NAME"]);
?>
<div class="news-detail">
	<div class="card">
  		<div class="card-header"></div>
 		 <div class="card-body">
			 <div class="card-img"><img class="card-img-top" src="<?echo($arResult["LINK_PICTURE"]);?>"></div>
			 <div class="card-data">
				<p>Год выпуска: <?echo($arResult["YEAR"]);?></p>
				<p>Цена: <?echo($arResult["PRICE"]);?></p>
			</div> 
		 </div>
		  <div class="card-footer"></div>
	</div>
</div>