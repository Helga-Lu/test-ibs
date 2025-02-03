<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$APPLICATION->IncludeComponent(
    "noteshop.d7:noteshop.model",
    "",
    array(
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "SEF_FOLDER" => $arParams["SEF_FOLDER"],
        "SEF_MODE" => $arParams["SEF_MODE"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "BRAND" => $arResult["VARIABLES"]["BRAND"],
        "MODEL" => $arResult["VARIABLES"]["MODEL"],
        "CATALOG_URL" => $arResult["ALIASES"]["CATALOG_URL"],
    ),
    $component
);
