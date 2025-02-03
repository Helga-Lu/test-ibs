<?

const ADMIN_MODULE_NAME = 'noteshop.d7';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

/** @global \CUser $USER */
/** @global \CMain $APPLICATION */

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile(__DIR__.'/notebook.php');

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
		'TAB' => GetMessage('NOTEBOOK_ADMIN_ENTITY_TITLE'),
		'TITLE' => GetMessage('NOTEBOOK_ADMIN_ENTITY_TITLE')
	)
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);

// init vars
$is_create_form = true;
$is_update_form = false;
$isEditMode = true;
$errors = array();
$models = array();
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
		'select' => array('ID', 'NAME', 'YEAR', 'PRICE', 'MODEL_ID'),
		'filter' => array('=ID' => $ID)
	);
	$notebook = \Noteshop\d7\DataTable::getList($filter)->fetch();

	if (!empty($notebook))
	{
		$is_update_form = true;
		$is_create_form = false;
		$mselected = $notebook['MODEL_ID'];
	}
}
	$res = \Noteshop\d7\ModelTable::getList(array('select' => array('*')));
	while ($row = $res->fetch())
	{
		$models[$row['ID']] = $row['NAME'];
	}

// default values for create form / page title
if ($is_create_form)
{
	$notebook = array_fill_keys(array('ID', 'NAME', 'YEAR', 'PRICE', 'MODEL_ID'), '');
	$APPLICATION->SetTitle(GetMessage('NOTEBOOK_ADMIN_ENTITY_EDIT_PAGE_TITLE_NEW'));
}
else
{
	$APPLICATION->SetTitle(GetMessage('NOTEBOOK_ADMIN_ENTITY_EDIT_PAGE_TITLE_EDIT', array('#NAME#' => $notebook['NAME'])));

}

// delete action
if ($is_update_form && $action === 'delete' && check_bitrix_sessid())
{
	$result = \Noteshop\d7\ModelTable::delete($notebook['ID']);
	if ($result->isSuccess())
	{
		\LocalRedirect('noteshop.php?lang='.LANGUAGE_ID);
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
		'YEAR' => trim($request->get('YEAR')),
		'PRICE' => trim($request->get('PRICE')),
		'MODEL_ID' => trim($request->get('MODEL_ID')),
	);

	if ($is_update_form)
	{
		$result = \Noteshop\d7\DataTable::update($ID, $data);
	}
	else
	{
		$result = \Noteshop\d7\DataTable::add($data);
		$ID = $result->getId();
	}

	if ($result->isSuccess())
	{
		// delete
		if (!empty($notUpdated))
		{
			foreach (array_keys($notUpdated) as $rid)
			{
				\Noteshop\d7\DataTable::delete($rid);
			}
		}

		if ($save != '')
		{
			\LocalRedirect('noteshop.php?lang='.LANGUAGE_ID);
		}
		else
		{
			\LocalRedirect('notebook_entity_edit.php?ID='.$ID.'&lang='.LANGUAGE_ID.'&'.$tabControl->ActiveTabParam());
		}
	}
	else
	{
		$errors = $result->getErrorMessages();
	}

	// rewrite original value by form value to restore form
	foreach ($data as $k => $v)
	{
		$notebook[$k] = $v;
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
		'TEXT'	=> GetMessage('NOTEBOOK_ADMIN_ROWS_RETURN_TO_LIST_BUTTON'),
		'TITLE'	=> GetMessage('NOTEBOOK_ADMIN_ROWS_RETURN_TO_LIST_BUTTON'),
		'LINK'	=> 'noteshop.php?lang='.LANGUAGE_ID,
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
	<input type="hidden" name="ID" value="<?= htmlspecialcharsbx($notebook['ID'])?>">
	<input type="hidden" name="lang" value="<?= LANGUAGE_ID?>">
	<?
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	?>
	<tr>
		<td width="40%"><strong><?= GetMessage('NOTEBOOK_ENTITY_NAME_FIELD')?></strong></td>
		<td><?
			if (!$isEditMode):
				?><?=htmlspecialcharsEx($notebook['NAME'])?><?
			else:
				?><input type="text" name="NAME" size="30" value="<?= htmlspecialcharsbx($notebook['NAME'])?>"><?
			endif;
		?></td>
	</tr>
	<tr>
		<td width="40%"><strong><?= GetMessage('NOTEBOOK_ENTITY_YEAR_FIELD')?></strong></td>
		<td><?
			if (!$isEditMode):
				?><?=htmlspecialcharsEx($notebook['YEAR'])?><?
			else:
				?><input type="text" name="YEAR" size="30" value="<?= htmlspecialcharsbx($notebook['YEAR'])?>"><?
			endif;
		?></td>
	</tr>
	<tr>
		<td width="40%"><strong><?= GetMessage('NOTEBOOK_ENTITY_PRICE_FIELD')?></strong></td>
		<td><?
			if (!$isEditMode):
				?><?=htmlspecialcharsEx($notebook['PRICE'])?><?
			else:
				?><input type="text" name="PRICE" size="30" value="<?= htmlspecialcharsbx($notebook['PRICE'])?>"><?
			endif;
		?></td>
	</tr>
	<tr>
		<td width="40%">
			<strong><?= GetMessage('NOTEBOOK_ENTITY_MODEL_FIELD')?></strong>
		</td>
		<td>
								<select name="MODEL_ID">
									<?foreach ($models as $mid => $mname):?>
									<option value="<?= $mid?>"<?= ($mselected == $mid ? ' selected="selected"' : '') ?>><?= $mname?></option>
									<?endforeach;?>
								</select>
		</td>
	</tr>
	<?
	$tabControl->Buttons(array('disabled' => !$isEditMode, 'back_url' => 'noteshop.php?lang='.LANGUAGE_ID));
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
