<?

const ADMIN_MODULE_NAME = 'noteshop.d7';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

/** @global \CUser $USER */
/** @global \CMain $APPLICATION */

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile(__DIR__.'/brands.php');

if (!$USER->IsAdmin())
{
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

if (!CModule::IncludeModule(ADMIN_MODULE_NAME))
{
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

// form
$aTabs = array(
	array(
		'DIV' => 'edit1',
		'TAB' => GetMessage('BRAND_ADMIN_ENTITY_TITLE'),
		'TITLE' => GetMessage('BRAND_ADMIN_ENTITY_TITLE')
	)
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);

// init vars
$is_create_form = true;
$is_update_form = false;
$isEditMode = true;
$errors = array();
$access = new \CAccess;
$ID = (int)$request->get('ID');
$save = trim((string)$request->get('save'));
$apply = trim((string)$request->get('apply'));
$action = trim((string)$request->get('action'));
$requestMethod = $request->getRequestMethod();

// get brand data
if ($ID > 0)
{
	$filter = array(
		'select' => array('ID', 'NAME'),
		'filter' => array('=ID' => $ID)
	);
	$brand = \Noteshop\d7\BrandTable::getList($filter)->fetch();

	if (!empty($brand))
	{
		$is_update_form = true;
		$is_create_form = false;
	}
}

// default values for create form / page title
if ($is_create_form)
{
	$brand = array_fill_keys(array('ID', 'NAME'), '');
	$APPLICATION->SetTitle(GetMessage('BRAND_ADMIN_ENTITY_EDIT_PAGE_TITLE_NEW'));
}
else
{
	$APPLICATION->SetTitle(GetMessage('BRAND_ADMIN_ENTITY_EDIT_PAGE_TITLE_EDIT', array('#NAME#' => $brand['NAME'])));

}

// delete action
if ($is_update_form && $action === 'delete' && check_bitrix_sessid())
{
	$result = \Noteshop\d7\BrandTable::delete($brand['ID']);
	if ($result->isSuccess())
	{
		\LocalRedirect('brands.php?lang='.LANGUAGE_ID);
	}
	else
	{
		$errors = $result->getErrorMessages();
	}
}

// save action
if (($save != '' || $apply != '') && $requestMethod == 'POST' && check_bitrix_sessid())
{
	$data = array(
		'NAME' => trim($request->get('NAME')),
	);

	if ($is_update_form)
	{
		$result = \Noteshop\d7\BrandTable::update($ID, $data);
	}
	else
	{
		$result = \Noteshop\d7\BrandTable::add($data);
		$ID = $result->getId();
	}

	if ($result->isSuccess())
	{
		// delete
		if (!empty($notUpdated))
		{
			foreach (array_keys($notUpdated) as $rid)
			{
				\Noteshop\d7\BrandTable::delete($rid);
			}
		}

		if ($save != '')
		{
			\LocalRedirect('brands.php?lang='.LANGUAGE_ID);
		}
		else
		{
			\LocalRedirect('brand_entity_edit.php?ID='.$ID.'&lang='.LANGUAGE_ID.'&'.$tabControl->ActiveTabParam());
		}
	}
	else
	{
		$errors = $result->getErrorMessages();
	}

	// rewrite original value by form value to restore form
	foreach ($data as $k => $v)
	{
		$brand[$k] = $v;
	}
}

// view
if ($request->get('mode') == 'list')
{
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_js.php');
}
else
{
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
}
// menu
$aMenu = array(
	array(
		'TEXT'	=> GetMessage('BRAND_ADMIN_ROWS_RETURN_TO_LIST_BUTTON'),
		'TITLE'	=> GetMessage('BRAND_ADMIN_ROWS_RETURN_TO_LIST_BUTTON'),
		'LINK'	=> 'brands.php?lang='.LANGUAGE_ID,
		'ICON'	=> 'btn_list',
	)
);
$adminContextMenu = new CAdminContextMenu($aMenu);
$adminContextMenu->Show();


if (!empty($errors))
{
	CAdminMessage::ShowMessage(join("\n", $errors));
}
?>
<form name="form1" method="POST" action="<?=$APPLICATION->GetCurPage()?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value="<?= htmlspecialcharsbx($brand['ID'])?>">
	<input type="hidden" name="lang" value="<?= LANGUAGE_ID?>">
	<?
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	?>
	<tr>
		<td width="40%"><strong><?= GetMessage('BRAND_ENTITY_NAME_FIELD')?></strong></td>
		<td><?
			if (!$isEditMode):
				?><?=htmlspecialcharsEx($brand['NAME'])?><?
			else:
				?><input type="text" name="NAME" size="30" value="<?= htmlspecialcharsbx($brand['NAME'])?>"><?
			endif;
		?></td>
	</tr>
	<?
	$tabControl->Buttons(array('disabled' => !$isEditMode, 'back_url' => 'brands.php?lang='.LANGUAGE_ID));
	$tabControl->End();
	?>
</form>
<?
if ($request->get('mode') == 'list')
{
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin_js.php');
}
else
{
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
}
