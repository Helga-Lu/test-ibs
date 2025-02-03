<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;
use \Bitrix\Main\Application;
class Noteshop extends CBitrixComponent
{
 public function executeComponent()
    {
        if ($this->arParams["SEF_MODE"] === "Y") {
            $componentPage = $this->sefMode();
        }
        if ($this->arParams["SEF_MODE"] != "Y") {
            $componentPage = $this->sefMode();
        }
        if (!$componentPage) {
            Tools::process404(
                $this->arParams["MESSAGE_404"],
                ($this->arParams["SET_STATUS_404"] === "Y"),
                ($this->arParams["SET_STATUS_404"] === "Y"),
                ($this->arParams["SHOW_404"] === "Y"),
                $this->arParams["FILE_404"]
            );
        }
        $this->IncludeComponentTemplate($componentPage);
    }


protected function sefMode()
    {
        $arComponentVariables = [
            'sort'
        ];
        $arDefaultVariableAliases404 = array(
            'model' => array(
                'ELEMENT_COUNT' => 'count',
            )
        );
        $arVariableAliases = CComponentEngine::makeComponentVariableAliases(
            $arDefaultVariableAliases404,
            $this->arParams["VARIABLE_ALIASES"]
        );
        $arDefaultUrlTemplates404 = [
            "brand" => "#BRAND#/",
            "notebook" => "detail/#NOTEBOOK#/",
			"model" => "#BRAND#/#MODEL#/",
        ];
        $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates(
            $arDefaultUrlTemplates404,
            $this->arParams["SEF_URL_TEMPLATES"]
        );
        $engine = new CComponentEngine($this);
        $arVariables = [];
        $componentPage = $engine->guessComponentPath(
            $this->arParams["SEF_FOLDER"],
            $arUrlTemplates,
            $arVariables
        );
        if ($componentPage == FALSE) {
            $componentPage = 'brandslist';
        }
        CComponentEngine::initComponentVariables(
            $componentPage,
            $arComponentVariables,
            $arVariableAliases,
            $arVariables
        );
        $this->arResult = [
            "VARIABLES" => $arVariables,
            "ALIASES" => $arVariableAliases
        ];
        return $componentPage;
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
            $this->arResult = $query->setSelect(array('*'))
                ->setFilter(array('=ID' => 1))
                ->setCacheTtl(3600)
                ->fetch();
            $this->arResult["SITE"] = json_decode($this->arResult['SITE']);
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
