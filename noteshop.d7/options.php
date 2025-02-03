<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
Loc::loadMessages(__FILE__);
$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT < "S") {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}
Loader::includeModule($module_id);
$aTabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => "Название вкладки в табах",
        "TITLE" => "Главное название в админке",
        "OPTIONS" => array(
            "Название секции checkbox",
            array(
                "note_checkbox",
                "Поясняющий текс элемента checkbox",
                "Y",
                array("checkbox"),
            ),
            "Название секции text",
            array(
                "note_text",
                "Поясняющий текс элемента text",
                "Жми!",
                array(
                    "text",
                    10,
                    50
                )
            ),
            "Название секции selectbox",
            array(
                "note_selectbox",
                "Поясняющий текс элемента selectbox",
                "460",
                array("selectbox", array(
                    "460" => "460Х306",
                    "360" => "360Х242",
                ))
            ),
            "Название секции multiselectbox",
            array(
                "note_multiselectbox",
                "Поясняющий текс элемента multiselectbox",
                "left, bottom",
                array("multiselectbox", array(
                    "left" => "Лево",
                    "right" => "Право",
                    "top" => "Верх",
                    "bottom" => "Низ",
                ))
            )
        )
    ),
    array(
        "DIV"   => "edit2",
        "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
    )
);
if ($request->isPost() && check_bitrix_sessid()) {
    foreach ($aTabs as $aTab) {
        foreach ($aTab["OPTIONS"] as $arOption) {
            if (!is_array($arOption)) {
                continue;
            }
            if ($request["Update"]) {
                $optionValue = $request->getPost($arOption[0]);
                if ($arOption[0] == "note_checkbox") {
                    if ($optionValue == "") {
                        $optionValue = "N";
                    }
                }
                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
            }
            if ($request["default"]) {
                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }
}
$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);
$tabControl->Begin();
?>
<form action="<? echo ($APPLICATION->GetCurPage()); ?>?mid=<? echo ($module_id); ?>&lang=<? echo (LANG); ?>" method="post">
    <? foreach ($aTabs as $aTab) {
        if ($aTab["OPTIONS"]) {
            $tabControl->BeginNextTab();
            __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        }
    }
    $tabControl->BeginNextTab();
    require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php";
    $tabControl->Buttons();
    echo (bitrix_sessid_post());
    ?>
    <input class="adm-btn-save" type="submit" name="Update" value="Применить" />
    <input type="submit" name="default" value="По умолчанию" />
</form>
<?
$tabControl->End();
