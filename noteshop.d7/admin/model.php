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

$APPLICATION->SetTitle(GetMessage('MODEL_ADMIN_MENU_TITLE'));

$sTableID = "tbl_model_entity";
$oSort = new CAdminSorting($sTableID, "NAME", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arHeaders = array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
	array("id"=>"NAME", "content"=>GetMessage('MODEL_ADMIN_ENTITY_TITLE'), "sort"=>"NAME", "default"=>true),
	array("id"=>"BRAND", "content"=>GetMessage('MODEL_ADMIN_ENTITY_TITLE'), "sort"=>"BRAND", "default"=>true),
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
$rsData = Noteshop\d7\ModelTable::getList([
	"select" => $lAdmin->GetVisibleHeaderColumns(),
	"order" => $getListOrder,
	'runtime' => [
	          'BRAND' => [
	          'data_type' => \Noteshop\d7\BrandTable::class,
			  'reference' => [
                	'=this.BRAND_ID' => 'ref.ID',
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
	$row->addViewField("BRAND", "<b>".$f_NOTESHOP_D7_MODEL_BRAND_NAME."</b>");
	$can_edit = true;

	$arActions = Array();
	$arActions[] = array(
		"ICON"=>"edit",
		"TEXT"=>GetMessage($can_edit ? "MAIN_ADMIN_MENU_EDIT" : "MAIN_ADMIN_MENU_VIEW"),
		"ACTION"=>$lAdmin->ActionRedirect("model_entity_edit.php?ID=".$f_ID)
	);

	$arActions[] = array(
		"ICON"=>"delete",
		"TEXT" => GetMessage("MAIN_ADMIN_MENU_DELETE"),
		"ACTION" => "if(confirm('".GetMessageJS('MODEL_ADMIN_DELETE_ENTITY_CONFIRM')."')) ".
			$lAdmin->ActionRedirect("model_entity_edit.php?action=delete&ID=".$f_ID.'&'.bitrix_sessid_get())
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
		"TEXT" => GetMessage('MODEL_ADMIN_ADD_ENTITY_BUTTON'),
		"TITLE" => GetMessage('MODEL_ADMIN_ADD_ENTITY_BUTTON'),
		"LINK" => "model_entity_edit.php?lang=" . LANGUAGE_ID,
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

