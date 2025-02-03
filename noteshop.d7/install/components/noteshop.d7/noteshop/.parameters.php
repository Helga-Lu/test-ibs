<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$arComponentParameters = array(
    'PARAMETERS' => array(
        'CACHE_TIME' => array(
            'DEFAULT' => 3600
        ),
		'SEF_MODE' => array(
            "brand" => [
                "NAME" => 'Cписок моделей производителя',
                "DEFAULT" => "#BRAND#/",
            ],
            "model" => [
                "NAME" => 'Cписок ноутбуков модели',
                "DEFAULT" => "#BRAND#/#MODEL#/",
            ],
			"notebook" => [
                "NAME" => 'Детальная страница ноутбука',
                "DEFAULT" => "detail/#NOTEBOOK#/",
            ]
        ),
    ),
);
