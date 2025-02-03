<?
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$aMenu = array(
    'parent_menu' => 'global_menu_services',
    'sort' => 1,
    'text' => "Тестовое задание",
    "items_id" => "menu_webforms",
    "icon" => "form_menu_icon",
);
// notebook
$aMenu["items"][] =  array(
    'text' => 'Ноутбуки',
    'url' => 'noteshop.php?lang=' . LANGUAGE_ID
);
// models
$aMenu["items"][] =  array(
    'text' => 'Модели',
    'url' => 'model.php?lang=' . LANGUAGE_ID
);
// brands
$aMenu["items"][] =  array(
    'text' => 'Бренды',
    'url' => 'brands.php?lang=' . LANGUAGE_ID
);
// дочерния ветка меню
$aMenu["items"][] =  array(
    'text' => 'Магазин ноутбуков',
    'url' => 'settings.php?lang=ru&mid=noteshop.d7'
);
return $aMenu;
