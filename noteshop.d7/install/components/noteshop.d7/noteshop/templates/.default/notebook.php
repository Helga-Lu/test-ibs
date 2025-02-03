<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$APPLICATION->IncludeComponent(
    "noteshop.d7:noteshop.notebook",
    "",
    array(
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "SEF_MODE" => $arParams["SEF_MODE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
        "NOTEBOOK" => $arResult["VARIABLES"]["NOTEBOOK"],
        "ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
        "SEF_FOLDER" => $arParams["SEF_FOLDER"],
    ),
    $component
);
