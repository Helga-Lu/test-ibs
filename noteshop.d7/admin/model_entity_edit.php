<?

const ADMIN_MODULE_NAME = 'noteshop.d7';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

/** @global \CUser $USER */
/** @global \CMain $APPLICATION */

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile(__DIR__.'/model.php');

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
		'TAB' => GetMessage('MODEL_ADMIN_ENTITY_TITLE'),
		'TITLE' => GetMessage('MODEL_ADMIN_ENTITY_TITLE')
	)
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);

// init vars
$is_create_form = true;
$is_update_form = false;
$isEditMode = true;
$errors = array();
$brands = array();
$brselected = 0;
$access = new \CAccess;
$ID = (int)$request->get('ID');
$save = trim((string)$request->get('save'));
$apply = trim((string)$request->get('apply'));
$action = trim((string)$request->get('action'));
$requestMethod = $request->getRequestMethod();

// get model data
if ($ID > 0)
{
	$filter = array(
		'select' => array('ID', 'NAME', 'BRAND_ID'),
		'filter' => array('=ID' => $ID)
	);
	$model = \Noteshop\d7\ModelTable::getList($filter)->fetch();

	if (!empty($model))
	{
		$is_update_form = true;
		$is_create_form = false;
		$brselected = $model['BRAND_ID'];
	}
}
	$res = \Noteshop\d7\BrandTable::getList(array('select' => array('*')));
	while ($row = $res->fetch())
	{
		$brands[$row['ID']] = $row['NAME'];
	}
	print_r($brselected);
	foreach($brands as $brid => $brname){
		echo($brid."-".$brname);
	}


// default values for create form / page title
if ($is_create_form)
{
	$model = array_fill_keys(array('ID', 'NAME', 'BRAND_ID'), '');
	$APPLICATION->SetTitle(GetMessage('MODEL_ADMIN_ENTITY_EDIT_PAGE_TITLE_NEW'));
}
else
{
	$APPLICATION->SetTitle(GetMessage('MODEL_ADMIN_ENTITY_EDIT_PAGE_TITLE_EDIT', array('#NAME#' => $model['NAME'])));

}

// delete action
if ($is_update_form && $action === 'delete' && check_bitrix_sessid())
{
	$result = \Noteshop\d7\ModelTable::delete($model['ID']);
	if ($result->isSuccess())
	{
		\LocalRedirect('model.php?lang='.LANGUAGE_ID);
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
		'BRAND_ID' => trim($request->get('BRAND_ID')),
	);

	if ($is_update_form)
	{
		$result = \Noteshop\d7\ModelTable::update($ID, $data);
	}
	else
	{
		$result = \Noteshop\d7\ModelTable::add($data);
		$ID = $result->getId();
	}

	if ($result->isSuccess())
	{
		// delete
		if (!empty($notUpdated))
		{
			foreach (array_keys($notUpdated) as $rid)
			{
				\Noteshop\d7\ModelTable::delete($rid);
			}
		}

		if ($save != '')
		{
			\LocalRedirect('model.php?lang='.LANGUAGE_ID);
		}
		else
		{
			\LocalRedirect('model_entity_edit.php?ID='.$ID.'&lang='.LANGUAGE_ID.'&'.$tabControl->ActiveTabParam());
		}
	}
	else
	{
		$errors = $result->getErrorMessages();
	}

	// rewrite original value by form value to restore form
	foreach ($data as $k => $v)
	{
		$model[$k] = $v;
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
		'TEXT'	=> GetMessage('MODEL_ADMIN_ROWS_RETURN_TO_LIST_BUTTON'),
		'TITLE'	=> GetMessage('MODEL_ADMIN_ROWS_RETURN_TO_LIST_BUTTON'),
		'LINK'	=> 'model.php?lang='.LANGUAGE_ID,
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
	<input type="hidden" name="ID" value="<?= htmlspecialcharsbx($model['ID'])?>">
	<input type="hidden" name="lang" value="<?= LANGUAGE_ID?>">
	<?
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	?>
	<tr>
		<td width="40%"><strong><?= GetMessage('MODEL_ENTITY_NAME_FIELD')?></strong></td>
		<td><?
			if (!$isEditMode):
				?><?=htmlspecialcharsEx($model['NAME'])?><?
			else:
				?><input type="text" name="NAME" size="30" value="<?= htmlspecialcharsbx($model['NAME'])?>"><?
			endif;
		?></td>
	</tr>
	<tr>
		<td width="40%">
			<strong><?= GetMessage('MODEL_ENTITY_BRAND_FIELD')?></strong>
		</td>
		<td>
									<select name="BRAND_ID">
									<?foreach ($brands as $brid => $brname):?>
									<option value="<?= $brid?>"<?= ($brselected == $brid ? ' selected="selected"' : '') ?>><?= $brname?></option>
									<?endforeach;?>
								</select>
		</td>
	</tr>
	<?
	$tabControl->Buttons(array('disabled' => !$isEditMode, 'back_url' => 'model.php?lang='.LANGUAGE_ID));
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
