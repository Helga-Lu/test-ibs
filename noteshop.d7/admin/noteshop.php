<?
const ADMIN_MODULE_NAME = 'noteshop.d7';

/** @global \CUser $USER */
/** @global \CMain $APPLICATION */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile(__DIR__.'/menu.php');

if (!$USER->IsAdmin())
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

if (!CModule::IncludeModule(ADMIN_MODULE_NAME))
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$APPLICATION->SetTitle(GetMessage('NOTESHOP_ADMIN_MENU_TITLE'));

$sTableID = "tbl_notebook_entity";
$oSort = new CAdminSorting($sTableID, "NAME", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arHeaders = array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
	array("id"=>"NAME", "content"=>GetMessage('NOTESHOP_ADMIN_ENTITY_TITLE'), "sort"=>"NAME", "default"=>true),
	array("id"=>"YEAR", "content"=>"YEAR", "sort"=>"YEAR", "default"=>true),
	array("id"=>"PRICE", "content"=>"PRICE", "sort"=>"PRICE", "default"=>true),
	array("id"=>"MODEL", "content"=>"MODEL", "sort"=>"MODEL", "default"=>true),
	array("id"=>"LINK_PICTURE", "content"=>"LINK_PICTURE", "sort"=>"", "default"=>true),
	//LINK_PICTURE
);

$lAdmin->AddHeaders($arHeaders);

$by = mb_strtoupper($oSort->getField());
$order = mb_strtoupper($oSort->getOrder());
$getListOrder = [
	$by => $order,
];
if ($by !== 'ID')
{
	$getListOrder['ID'] = 'ASC';
}
// select data
$rsData = Noteshop\d7\DataTable::getList([
	'select' => $lAdmin->GetVisibleHeaderColumns(),
	'order' => $getListOrder,
	'runtime' => [
	          'MODEL' => [
	          'data_type' => \Noteshop\d7\ModelTable::class,
			  'reference' => [
                	'=this.MODEL_ID' => 'ref.ID',
            	],
				['join_type' => 'LEFT']
			]
	   ],
]);

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

// build list
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PAGES")));
while($arRes = $rsData->NavNext(true, "f_"))
{
	$row = $lAdmin->AddRow($f_ID, $arRes);
	$row->addViewField("MODEL", "<b>".$f_NOTESHOP_D7_DATA_MODEL_NAME."</b>");
	$row->addViewField("LINK_PICTURE", '<img src="'.$f_LINK_PICTURE.'" width="300px">');
	$can_edit = true;

	$arActions = Array();

	$arActions[] = array(
		"ICON"=>"edit",
		"TEXT"=>GetMessage($can_edit ? "MAIN_ADMIN_MENU_EDIT" : "MAIN_ADMIN_MENU_VIEW"),
		"ACTION"=>$lAdmin->ActionRedirect("notebook_entity_edit.php?ID=".$f_ID)
	);

	$arActions[] = array(
		"ICON"=>"delete",
		"TEXT" => GetMessage("MAIN_ADMIN_MENU_DELETE"),
		"ACTION" => "if(confirm('".GetMessageJS('NOTEBOOK_ADMIN_DELETE_ENTITY_CONFIRM')."')) ".
			$lAdmin->ActionRedirect("notebook_entity_edit.php?action=delete&ID=".$f_ID.'&'.bitrix_sessid_get())
	);

	$row->AddActions($arActions);
}
// view
if ($lAdmin->isListMode())
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
}
else
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

	// menu
	$aMenu = [];
	$aMenu[] = [
		"TEXT" => GetMessage('NOTEBOOK_ADMIN_ADD_ENTITY_BUTTON'),
		"TITLE" => GetMessage('NOTEBOOK_ADMIN_ADD_ENTITY_BUTTON'),
		"LINK" => "notebook_entity_edit.php?lang=" . LANGUAGE_ID,
		"ICON" => "btn_new",
	];

	$adminContextMenu = new CAdminContextMenu($aMenu);
	$adminContextMenu->Show();
}

$lAdmin->CheckListMode();

$lAdmin->DisplayList();


if ($lAdmin->isListMode())
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
}
else
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
}
