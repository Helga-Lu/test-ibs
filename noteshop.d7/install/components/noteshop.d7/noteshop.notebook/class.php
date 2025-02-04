<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;
use \Bitrix\Main\Application;
class NoteshopTemplate extends CBitrixComponent
{
    public function executeComponent()
    {
        try {
            $this->checkModules();
            $this->getResult();
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }
    public function onIncludeComponentLang()
    {
        Loc::loadMessages(__FILE__);
    }
    protected function checkModules()
    {
        if (!Loader::includeModule('noteshop.d7')) {
            throw new SystemException(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
        }
    }
    public function onPrepareComponentParams($arParams)
    {
        if (!isset($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = 3600;
        } else {
            $arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
        }
        return $arParams;
    }
    protected function getResult()
    {

			$this->arResult["ID"] = null;
		if(preg_match("~^/noteshop/detail/([0-9]+)/$~",$_SERVER["REQUEST_URI"],$match))
			{
				$this->arResult["ID"] = $match[1];
			}
            $query = new Bitrix\Main\Entity\Query(
                \Noteshop\d7\DataTable::getEntity()
            );
            $this->arResult = $query->setSelect(array('*'))
                ->setFilter(array('=ID' => $this->arResult["ID"]))
                ->setCacheTtl(3600)
                ->fetch();
			//$this->arResult["SITE"] = json_decode($this->arResult['SITE']);
            if (!empty($this->arResult["EXCEPTIONS"])) {
                $this->arResult["EXCEPTIONS"] = preg_split("/\r\n|\n|\r/", $this->arResult['EXCEPTIONS']);
            }

            $this->arResult["SETTINGS"] = \Bitrix\Main\Config\Option::getForModule("noteshop.d7");


        $this->IncludeComponentTemplate();
    }
}

