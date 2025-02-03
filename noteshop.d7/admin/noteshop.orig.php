<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';
use \Bitrix\Main\Loader;
$POST_RIGHT = $APPLICATION->GetGroupRight("noteshop.d7");
if ($POST_RIGHT == "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}
$APPLICATION->SetTitle("Настройка NoteShop");
IncludeModuleLangFile(__FILE__);
$aTabs = array(
    array(
        "TAB" => "Параметры",
        "TITLE" => "Параметры вывода Noteshop"
    )
);
$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);
Loader::includeModule("noteshop.d7");
if (
    $REQUEST_METHOD == "POST"
    &&
    $save != ""
    &&
    $POST_RIGHT == "W"
    &&
    check_bitrix_sessid()
) {
    $notebookTable = new \Noteshop\d7\DataTable;
    $arFields = array(
	"NAME" => htmlspecialchars($NAME),
        "MODEL" => htmlspecialchars($MODEL),
        "YEAR" => htmlspecialchars($YEAR),
        "PRICE" => htmlspecialchars($PRICE),
        "LINK_PICTURE" => htmlspecialchars($LINK_PICTURE),
        "ALT_PICTURE" => htmlspecialchars($ALT_PICTURE),
    );
    $res = $notebookTable->Update(1, $arFields);
    if ($res->isSuccess()) {
        if ($save != "") {
            LocalRedirect("/bitrix/admin/noteshop.php?mess=ok&lang=" . LANG);
        }
    }
    if (!$res->isSuccess()) {
        if ($e = $APPLICATION->GetException())
            $message = new CAdminMessage("Ошибка сохранения: ", $e);
        else {
            $mess = print_r($res->getErrorMessages(), true);
            $message = new CAdminMessage("Ошибка сохранения: " . $mess);
        }
    }
}
$result = \Noteshop\d7\DataTable::GetByID(1);
if ($result->getSelectedRowsCount()) {
    $notebookTable = $result->fetch();
    $str_LINK_PICTURE = $notebookTable["LINK_PICTURE"];
    $str_ALT_PICTURE = $notebookTable["ALT_PICTURE"];
    $str_NAME = $notebookTable["NAME"];
    $str_MODEL = $notebookTable["MODEL"];
    $str_YEAR = $notebookTable["YEAR"];
    $str_PRICE = $notebookTable["PRICE"];
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
if ($_REQUEST["mess"] == "ok") {
    CAdminMessage::ShowMessage(array("MESSAGE" => "Сохранено успешно", "TYPE" => "OK"));
}
if ($message) {
    echo $message->Show();
}
if ($notebookTable->LAST_ERROR != "") {
    CAdminMessage::ShowMessage($notebookTable->LAST_ERROR);
}
?>
<form method="POST" action="<?= $APPLICATION->GetCurPage() ?>" ENCTYPE="multipart/form-data" name="post_form">
    <?
    echo bitrix_sessid_post();
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td width="40%"><?= "Название NAME" ?></td>
        <td width="60%"><input type="text" name="NAME" value="<?= $str_NAME ?>" /></td>
    </tr>
    <tr>
        <td width="40%"><?= "Ссылка на картинку" ?></td>
        <td width="60%"><input type="text" name="LINK_PICTURE" value="<?= $str_LINK_PICTURE ?>" /></td>
    </tr>
    <tr>
        <td width="40%"><?= "Alt картинки" ?></td>
        <td width="60%"><input type="text" name="ALT_PICTURE" value="<?= $str_ALT_PICTURE ?>" /></td>
    </tr>
    <tr>
        <td width="40%"><?= "Год" ?></td>
		<td width="60%"><input type="text" name="YEAR" value="<?= $str_YEAR ?>" /></td>
    </tr>
    <tr>
        <td width="40%"><?= "Значение PRICE" ?></td>
        <td width="60%"><input type="text" name="PRICE" value="<?= $str_PRICE ?>" /></td>
    </tr>
    <?
    $tabControl->Buttons();
    ?>
    <input class="adm-btn-save" type="submit" name="save" value="Сохранить настройки" />
    <?
    $tabControl->End();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
    ?>
