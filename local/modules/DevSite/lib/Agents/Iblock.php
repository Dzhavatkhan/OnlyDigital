<?php


namespace Agents;

use Bitrix\Main\Loader;
use CIBlock;

class Iblock
{

    public function GetBlock()
    {
        if (!Loader::includeModule("iblock")) {
            return false;
        }
        $arFilter = array(
            "CODE" => "LOG",
            "ACTIVE" => "Y"
        );

        $res = CIBlock::GetList(array(), $arFilter);
        $iblock = $res->Fetch();
        return $iblock;
    }
    public function CleanLog()
    {
        global $DB;
        if (Loader::includeModule("iblock")) {
            $iblock = new Iblock();
            $iblock = $iblock->GetBlock();
            $format = $DB->DateFormatToPHP(\CLang::GetDateFormat('SHORT'));
            $logs = \CIBlockElement::GetList(['TIMESTAMP_X' => 'ASC'], [
                'IBLOCK_ID' => $iblock['ID'],
                '<TIMESTAMP_X' => date($format, strtotime('-1 months')),
            ], false, false, ['ID', 'IBLOCK_ID']);

            print_r($logs);
        }
    }
}
