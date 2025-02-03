<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
    'NAME' => 'Noteshop (комплексный)',                                  
    'DESCRIPTION' => 'Компонент для Noteshop.d7',
    'ICON' => '/images/icon.gif',                                    
    'CACHE_PATH' => 'Y',                                               
    'SORT' => 40,                                                  
    'COMPLEX' => 'Y',                                                 
    'PATH' => array(                                            
        'ID' => 'noteshop_components',                                   
        'NAME' => 'Компоненты от OL',                       
        'CHILD' => array(                                              
            'ID' => 'noteshop_block',                            
            'NAME' => 'Блок'
        )
    )
);
