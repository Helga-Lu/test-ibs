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
        if ($this->startResultCache()) {
            $cachePath = "/" . SITE_ID . $this->GetRelativePath();
            $taggedCache = Application::getInstance()->getTaggedCache();
            $taggedCache->startTagCache($cachePath);
            $taggedCache->registerTag('noteshop');
            $query = new Bitrix\Main\Entity\Query(
                \Noteshop\d7\DataTable::getEntity()
            );
            $res = $query->setSelect(array('*'))
                ->setCacheTtl(3600)
                ->exec();
			//$this->arResult["SITE"] = json_decode($this->arResult['SITE']);
			$this->arResult["Data"] = array();
			while ($row = $res->fetch()) {
				array_push($this->arResult["Data"], $row);
			}

            if (!empty($this->arResult["EXCEPTIONS"])) {
                $this->arResult["EXCEPTIONS"] = preg_split("/\r\n|\n|\r/", $this->arResult['EXCEPTIONS']);
            }
            $taggedCache->endTagCache();
            $this->arResult["SETTINGS"] = \Bitrix\Main\Config\Option::getForModule("noteshop.d7");
            $this->EndResultCache();
        }
        $this->IncludeComponentTemplate();
    }
}

