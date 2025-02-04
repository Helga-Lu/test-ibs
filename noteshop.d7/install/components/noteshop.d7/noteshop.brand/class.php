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
			{$this->arResult["BRID"] = null;
			if(preg_match("~^/noteshop/([0-9]+)/$~",$_SERVER["REQUEST_URI"],$match))
			{
				$this->arResult["BRID"] = $match[1];
			}
            $query = new Bitrix\Main\Entity\Query(
                \Noteshop\d7\ModelTable::getEntity()
            );
            $res = $query->setSelect(array('*'))
				->setFilter(array("=BRAND_ID" => $this->arResult["BRID"]))
                ->setCacheTtl(3600)
                ->exec();

			$this->arResult["TABLE"] = array();

			while ($row = $res->fetch()) {
				//array_push($this->arResult["TABLE"], ["DATA"=>$row,"ACTIONS"=>[]]);
			array_push($this->arResult["TABLE"], $row);
				//$this->arResult["TABLE"] = $this->arResult["TABLE"] + ["DATA"=>$row,"ACTIONS"=>[]];
			}


            if (!empty($this->arResult["EXCEPTIONS"])) {
                $this->arResult["EXCEPTIONS"] = preg_split("/\r\n|\n|\r/", $this->arResult['EXCEPTIONS']);
            }

            $this->arResult["SETTINGS"] = \Bitrix\Main\Config\Option::getForModule("noteshop.d7");
			$this->IncludeComponentTemplate();
        }

}

