<?php

use Bitrix\Main\Localization\Loc;

class DevSite extends \CModule
{
	public $MODULE_ID = "DevSite";
	public $MODULE_GROUP_RIGHTS = "Y";

	private $errors = [];

	public function __construct()
	{
		$arModuleVersion = [];

		include(__DIR__ . '/version.php');

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = "DevSite is developer-local's module";
		$this->MODULE_DESCRIPTION = Loc::getMessage("empty description");
	}


	public function InstallDB($install_wizard = true)
	{
		\Bitrix\Main\ModuleManager::registerModule('DevSite');

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		\Bitrix\Main\ModuleManager::unRegisterModule('DevSite');

		return true;
	}

	public function InstallFiles()
	{
		return true;
	}

	public function UnInstallFiles()
	{
		return true;
	}

	public function DoInstall()
	{
		$this->InstallFiles();
		$this->InstallDB(false);
	}

	public function DoUninstall()
	{
		$this->UnInstallDB(false);
	}
}
