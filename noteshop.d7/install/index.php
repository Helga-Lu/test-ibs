<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Application;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Loader;
use \Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

class Noteshop_D7 extends CModule
{
    public  $MODULE_ID;
    public  $MODULE_VERSION;
    public  $MODULE_VERSION_DATE;
    public  $MODULE_NAME;
    public  $MODULE_DESCRIPTION;
    public  $PARTNER_NAME;
    public  $PARTNER_URI;
    public  $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
    public  $MODULE_GROUP_RIGHTS;
    public  $errors;

    function __construct()
    {
        $arModuleVersion = array();
        include_once(__DIR__ . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_ID = "noteshop.d7";
        $this->MODULE_NAME = "Модуль Магазин ноутбуков";
        $this->MODULE_DESCRIPTION = "Тестовое задание для IBS";
        $this->PARTNER_NAME = "Ольга Лугаськова";
        $this->PARTNER_URI = "https://bitrix.local.ru";
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    function DoInstall()
    {
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        global $APPLICATION;
        if ($request["step"] < 2) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('INSTALL_TITLE_STEP_1'),
                __DIR__ . '/instalInfo-step1.php'
            );
        }
        if ($request["step"] == 2) {
            ModuleManager::RegisterModule("noteshop.d7");
            $this->InstallDB();
            //$this->InstallEvents();
            $this->InstallFiles();
            //$this->installAgents();
            if ($request["add_data"] == "Y") {
                $this->addData();
            }
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('INSTALL_TITLE_STEP_2'),
                __DIR__ . '/instalInfo-step2.php'
            );
        }

        return true;
    }

    function DoUninstall()
    {
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        global $APPLICATION;
        if ($request["step"] < 2) {
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('DEINSTALL_TITLE_1'),
                __DIR__ . '/deInstalInfo-step1.php'
            );
        }
        if ($request["step"] == 2) {
            //$this->UnInstallDB();
            if ($request["save_data"] == "Y") {
                $this->UnInstallDB();
            }
            //$this->UnInstallEvents();
            //$this->UnInstallFiles();
            //$this->unInstallAgents();
            ModuleManager::UnRegisterModule("noteshop.d7");

            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('DEINSTALL_TITLE_2'),
                __DIR__ . '/deInstalInfo-step2.php'
            );
        }
        return true;
    }


    function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);
        if (!Application::getConnection(\Noteshop\d7\DataTable::getConnectionName())->isTableExists(Base::getInstance("\Noteshop\d7\DataTable")->getDBTableName())) {
            Base::getInstance("\Noteshop\d7\DataTable")->createDbTable();
        }

        if (!Application::getConnection(\Noteshop\d7\DataTable::getConnectionName())->isTableExists(Base::getInstance("\Noteshop\d7\ModelTable")->getDBTableName())) {
            Base::getInstance("\Noteshop\d7\ModelTable")->createDbTable();
        }
		if (!Application::getConnection(\Noteshop\d7\DataTable::getConnectionName())->isTableExists(Base::getInstance("\Noteshop\d7\BrandTable")->getDBTableName())) {
            Base::getInstance("\Noteshop\d7\BrandTable")->createDbTable();
        }
    }

    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);
        Application::getConnection(\Noteshop\d7\DataTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\Noteshop\d7\DataTable")->getDBTableName());
        Application::getConnection(\Noteshop\d7\DataTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\Noteshop\d7\ModelTable")->getDBTableName());
		Application::getConnection(\Noteshop\d7\DataTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\Noteshop\d7\BrandTable")->getDBTableName());
        Option::delete($this->MODULE_ID);
    }

    function InstallEvents()
    {
        EventManager::getInstance()->registerEventHandler(
            $this->MODULE_ID,
            "OnSomeEvent",
            $this->MODULE_ID,
            "\Noteshop\d7\Main",
            'get'
        );

        EventManager::getInstance()->registerEventHandler(
            $this->MODULE_ID,
            "\Noteshop\d7\Data::OnBeforeUpdate",
            $this->MODULE_ID,
            "\Noteshop\d7\Events",
            'eventHandler'
        );

        return true;
    }

    function UnInstallEvents()
    {
        EventManager::getInstance()->unRegisterEventHandler(
            $this->MODULE_ID,
            "OnSomeEvent",
            $this->MODULE_ID,
            "\Noteshop\d7\Main",
            'get'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            $this->MODULE_ID,
            "\Noteshop\d7\Data::OnBeforeUpdate",
            $this->MODULE_ID,
            "\Noteshop\d7\Events",
            'eventHandler'
        );

        return true;
    }

    function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin",
            true, // перезаписывает файлы
            true  // копирует рекурсивно
        );

        CopyDirFiles(
            __DIR__ . "/components",
            $_SERVER["DOCUMENT_ROOT"] . "/local/components",
            true,
            true  
        );

        CopyDirFiles(
            __DIR__ . '/images',
			$_SERVER["DOCUMENT_ROOT"] . '/local/images',
            true,
            true
        );

        return true;
    }

    function UnInstallFiles()
    {
         DeleteDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"
        );

        if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/local/components/" . $this->MODULE_ID)) {
            DeleteDirFilesEx(
                "/bitrix/components/" . $this->MODULE_ID
            );
        }

         DeleteDirFiles(
            __DIR__ . "/files",
            $_SERVER["DOCUMENT_ROOT"] . "/"
        );

        return true;
    }

    function addData()
    {
        Loader::includeModule($this->MODULE_ID);

        \Noteshop\d7\DataTable::add(
            array(
                "NAME" => "MacBook Air M1",
                "YEAR" => 2020,
                "PRICE" => 72294,
                "LINK_PICTURE" => "/local/images/1.webp",
                "ALT_PICTURE" => "MacBook Air M1",
                "MODEL_ID" => "1",
            )
        );
		\Noteshop\d7\DataTable::add(
			array(
                "NAME" => "MacBook Air M3",
                "YEAR" => 2024,
                "PRICE" => 134260,
                "LINK_PICTURE" => "/local/images/2.webp",
                "ALT_PICTURE" => "MacBook Air M3",
                "MODEL_ID" => "1",
            )
        );
        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "MacBook Pro 16 M2",
                "YEAR" => 2022,
                "PRICE" => 221681,
                "LINK_PICTURE" => "/local/images/3.webp",
                "ALT_PICTURE" => "MacBook Pro 16 M2",
                "MODEL_ID" => "2",
            )
        );
        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "MacBook Pro 14 m4",
                "YEAR" => 2024,
                "PRICE" => 197875,
                "LINK_PICTURE" => "/local/images/4.webp",
                "ALT_PICTURE" => "MacBook Pro 14 m4",
                "MODEL_ID" => "2",
            )
        );

        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "Acer Aspire 3",
                "YEAR" => 2023,
                "PRICE" => 44777,
                "LINK_PICTURE" => "/local/images/5.webp",
                "ALT_PICTURE" => "Acer Aspire 3",
                "MODEL_ID" => "3",
            )
        );
        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "Acer Extensa",
                "YEAR" => 2023,
                "PRICE" => 38603,
                "LINK_PICTURE" => "/local/images/6.webp",
                "ALT_PICTURE" => "Acer Extensa",
                "MODEL_ID" => "4",
            )
        );
        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "Acer Nitro",
                "YEAR" => 2024,
                "PRICE" => 146404,
                "LINK_PICTURE" => "/local/images/7.webp",
                "ALT_PICTURE" => "Acer Nitro",
                "MODEL_ID" => "5",
            )
        );
        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "Acer Swift Edge",
                "YEAR" => 2024,
                "PRICE" => 94738,
                "LINK_PICTURE" => "/local/images/8.webp",
                "ALT_PICTURE" => "Acer Swift Edge",
                "MODEL_ID" => "6",
            )
        );

        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "ASUS ExpertBook",
                "YEAR" => 2022,
                "PRICE" => 75289,
                "LINK_PICTURE" => "/local/images/9.webp",
                "ALT_PICTURE" => "ASUS ExpertBook",
                "MODEL_ID" => "7",
            )
        );
        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "ASUS ROG Strix",
                "YEAR" => 2023,
                "PRICE" => 180226,
                "LINK_PICTURE" => "/local/images/10.webp",
                "ALT_PICTURE" => "ASUS ROG Strix",
                "MODEL_ID" => "8",
            )
        );
        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "ASUS TUF F17",
                "YEAR" => 2024,
                "PRICE" => 80558,
                "LINK_PICTURE" => "/local/images/11.webp",
                "ALT_PICTURE" => "ASUS TUF F17",
                "MODEL_ID" => "9",
            )
        );
        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "ASUS VivoBook",
                "YEAR" => 2024,
                "PRICE" => 59322,
                "LINK_PICTURE" => "/local/images/12.webp",
                "ALT_PICTURE" => "ASUS VivoBook",
                "MODEL_ID" => "10",
            )
        );

        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "HUAWEI MateBook D",
                "YEAR" => 2024,
                "PRICE" => 63280,
                "LINK_PICTURE" => "/local/images/13.webp",
                "ALT_PICTURE" => "HUAWEI MateBook D",
                "MODEL_ID" => "11",
            )
        );
        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "HUAWEI MateBook E Go",
                "YEAR" => 2023,
                "PRICE" => 64999,
                "LINK_PICTURE" => "/local/images/14.webp",
                "ALT_PICTURE" => "HUAWEI MateBook E Go",
                "MODEL_ID" => "11",
            )
        );
        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "HUAWEI MateBook MDF-X",
                "YEAR" => 2021,
                "PRICE" => 69794,
                "LINK_PICTURE" => "/local/images/15.webp",
                "ALT_PICTURE" => "Huawei MateBook MDF-X",
                "MODEL_ID" => "11",
            )
        );
        \Noteshop\d7\DataTable::add(
			array(
                "NAME" => "HUAWEI MateBook X",
                "YEAR" => 2023,
                "PRICE" => 212849,
                "LINK_PICTURE" => "/local/images/16.webp",
                "ALT_PICTURE" => "HUAWEI MateBook X",
                "MODEL_ID" => "11",
            )
        );


        \Noteshop\d7\ModelTable::add(
            array(
                "ID" => 1,
                "NAME" => "AIR",
                "BRAND_ID" => "1",
            )
        );
        \Noteshop\d7\ModelTable::add(
            array(
                "ID" => 2,
                "NAME" => "PRO",
                "BRAND_ID" => "1",
            )
        );

        \Noteshop\d7\ModelTable::add(
            array(
                "ID" => 3,
                "NAME" => "Aspire",
                "BRAND_ID" => "2",
            )
        );
        \Noteshop\d7\ModelTable::add(
            array(
                "ID" => 4,
                "NAME" => "Extensa",
                "BRAND_ID" => "2",
            )
        );
        \Noteshop\d7\ModelTable::add(
            array(
                "ID" => 5,
                "NAME" => "Nitro",
                "BRAND_ID" => "2",
            )
        );
        \Noteshop\d7\ModelTable::add(
            array(
                "ID" => 6,
                "NAME" => "Swift",
                "BRAND_ID" => "2",
            )
        );

        \Noteshop\d7\ModelTable::add(
            array(
                "ID" => 7,
                "NAME" => "ExpertBook",
                "BRAND_ID" => "3",
            )
        );
        \Noteshop\d7\ModelTable::add(
            array(
                "ID" => 8,
                "NAME" => "Rog",
                "BRAND_ID" => "3",
            )
        );
        \Noteshop\d7\ModelTable::add(
            array(
                "ID" => 9,
                "NAME" => "TUF",
                "BRAND_ID" => "3",
            )
        );
        \Noteshop\d7\ModelTable::add(
            array(
                "ID" => 10,
                "NAME" => "VivoBook",
                "BRAND_ID" => "3",
            )
        );

        \Noteshop\d7\ModelTable::add(
            array(
                "ID" => 11,
                "NAME" => "MateBook",
                "BRAND_ID" => "4",
            )
        );

        \Noteshop\d7\BrandTable::add(
            array(
                "ID" => 1,
                "NAME" => "APPLE",
            )
        );
        \Noteshop\d7\BrandTable::add(
            array(
                "ID" => 2,
                "NAME" => "ACER",
            )
        );
        \Noteshop\d7\BrandTable::add(
            array(
                "ID" => 3,
                "NAME" => "ASUS",
            )
        );
        \Noteshop\d7\BrandTable::add(
            array(
                "ID" => 4,
                "NAME" => "HUAWEI",
            )
        );

        return true;
    }

    function installAgents()
    {
        \CAgent::AddAgent(
            "\Noteshop\d7\Agent::superAgent();",
            $this->MODULE_ID,
            "N",
            120,
            "",
            "Y",
            "",
            100
        );
    }

    function unInstallAgents()
    {
        \CAgent::RemoveModuleAgents($this->MODULE_ID);
    }
}
