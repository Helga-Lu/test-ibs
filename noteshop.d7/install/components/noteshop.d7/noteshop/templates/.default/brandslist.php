<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$APPLICATION->IncludeComponent(
    "noteshop.d7:noteshop.brandslist",
    "",
    array(
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "SEF_MODE" => $arParams["SEF_MODE"],
    ),
    $component
);
