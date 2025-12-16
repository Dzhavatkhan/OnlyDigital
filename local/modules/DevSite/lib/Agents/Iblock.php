<?php

namespace Agents;

use Bitrix\Main\Loader;
use CIBlock;
use CIBlockElement;

class Iblock
{
    public static function GetBlock()
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
    
    public static function CleanLog()
    {
        if (!Loader::includeModule("iblock")) {
            return "Agents\\Iblock::CleanLog();"; // Все равно возвращаем строку
        }
        
        $logBlock = self::GetBlock();
        if (!$logBlock) {
            \CEventLog::Add([
                'SEVERITY' => 'ERROR',
                'AUDIT_TYPE_ID' => 'AGENT_ERROR',
                'MODULE_ID' => 'main',
                'DESCRIPTION' => 'Не найден инфоблок LOG'
            ]);
            return "Agents\\Iblock::CleanLog();";
        }
        
        $iBlockId = $logBlock['ID'];

        $res = CIBlockElement::GetList(
            ["ID" => "DESC"],
            ['IBLOCK_ID' => $iBlockId],
            false,
            ["nTopCount" => 1000],
            ['ID']
        );
        
        $boxId = [];
        while ($el = $res->Fetch()) {
            $boxId[] = $el['ID'];
        }

        $skipDel = array_slice($boxId, 10);
        $deletedCount = 0;
        
        if (!empty($skipDel)) {
            foreach ($skipDel as $id) {
                if (CIBlockElement::Delete($id)) {
                    $deletedCount++;
                }
            }
        }

        \CEventLog::Add([
            'SEVERITY' => 'INFO',
            'AUDIT_TYPE_ID' => 'AGENT_RESULT',
            'MODULE_ID' => 'main',
            'DESCRIPTION' => "CleanLog: удалено $deletedCount старых элементов"
        ]);

        return "Agents\\Iblock::CleanLog();";
    }
    
    public function getLogIblockId()
    {
        $iblock = self::GetBlock();
        return $iblock ? $iblock['ID'] : false;
    }
}