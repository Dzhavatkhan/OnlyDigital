<?php

use Agents\Iblock;

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/DevSite/lib/Agents/Iblock.php');

AddEventHandler("iblock", "OnAfterIBlockElementAdd", "LogElementChange");
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "LogElementChange");

$GLOBALS['CACHE_IBLOCK_DATA'] = [];

$GLOBALS['CACHE_LOG_SECTIONS'] = [];

function getIblockData($iblockId)
{
    global $CACHE_IBLOCK_DATA;

    if (!isset($CACHE_IBLOCK_DATA[$iblockId])) {
        $res = CIBlock::GetByID($iblockId);
        $CACHE_IBLOCK_DATA[$iblockId] = $res->Fetch();
    }

    return $CACHE_IBLOCK_DATA[$iblockId];
}

function getOrCreateLogSection($logIblockId, $iblockName, $iblockCode)
{
    global $CACHE_LOG_SECTIONS;

    if (isset($CACHE_LOG_SECTIONS[$iblockCode])) {
        return $CACHE_LOG_SECTIONS[$iblockCode];
    }

    $res = CIBlockSection::GetList(
        [],
        ['IBLOCK_ID' => $logIblockId, 'CODE' => $iblockCode],
        false,
        ['ID']
    )->Fetch();

    if ($res) {
        $sectionId = $res['ID'];
    } else {
        $sect = new CIBlockSection();
        $sectionId = $sect->Add([
            'IBLOCK_ID' => $logIblockId,
            'NAME' => $iblockName,
            'CODE' => $iblockCode,
        ]);
        if (!$sectionId) return false;
    }

    $CACHE_LOG_SECTIONS[$iblockCode] = $sectionId;

    return $sectionId;
}

function buildSectionPathOptimized($sectionId, $iblockId)
{
    $path = [];

    $maxDepth = 10;
    $currentDepth = 0;

    while ($sectionId && $currentDepth < $maxDepth) {
        $res = CIBlockSection::GetList(
            [],
            ['ID' => $sectionId, 'IBLOCK_ID' => $iblockId],
            false,
            ['ID', 'NAME', 'IBLOCK_SECTION_ID']
        )->Fetch();

        if (!$res) break;

        array_unshift($path, $res['NAME']);
        $sectionId = $res['IBLOCK_SECTION_ID'];
        $currentDepth++;
    }

    return implode(" → ", $path);
}

function LogElementChange($arFields)
{
    if (!\Bitrix\Main\Loader::includeModule('iblock')) {
        return;
    }

    static $logIblockId = null;

    if ($logIblockId === null) {
        $log = new Iblock();
        $iblockData = $log->GetBlock();
        $logIblockId = $iblockData ? $iblockData['ID'] : false;
    }

    if (!$logIblockId || $arFields['IBLOCK_ID'] == $logIblockId) {
        return;
    }


    $elementId = $arFields['ID'];
    $iblockId = $arFields['IBLOCK_ID'];

    $elementName = $arFields['NAME'] ?? $arFields['~NAME'] ?? 'Без названия';
    $sectionId = $arFields['IBLOCK_SECTION_ID'] ?? null;

    $actionDate = $arFields['TIMESTAMP_X'] ?: $arFields['DATE_CREATE'];

    $iblockData = getIblockData($iblockId);
    if (!$iblockData) return;

    $iblockName = $iblockData['NAME'];
    $iblockCode = $iblockData['CODE'] ?: $iblockId;

    $logSectionId = getOrCreateLogSection($logIblockId, $iblockName, $iblockCode);
    if (!$logSectionId) return;

    $sectionPath = '';
    if ($sectionId) {
        $sectionPath = buildSectionPathOptimized($sectionId, $iblockId);
    }

    $announcement = trim(implode(" → ", array_filter([
        $iblockName,
        $sectionPath,
        $elementName
    ])), " → ");


    $activeFrom = $actionDate;

    $el = new CIBlockElement;
    $result = $el->Add([
        'IBLOCK_ID' => $logIblockId,
        'NAME' => (string)$elementId,
        'ACTIVE_FROM' => $activeFrom,
        'PREVIEW_TEXT' => $announcement,
        'IBLOCK_SECTION_ID' => $logSectionId,
        'PROPERTY_VALUES' => [
            'ORIGINAL_ELEMENT_ID' => $elementId,
            'ORIGINAL_IBLOCK_ID' => $iblockId,
            'ACTION_TYPE' => $arFields['TIMESTAMP_X'] ? 'UPDATE' : 'ADD',
        ],
        'NAME' => $elementId, // имя — ID
    ], false, false); // отключаем отправку почты и индексацию

    if (!$result) {
        \CEventLog::Add([
            'SEVERITY' => 'ERROR',
            'AUDIT_TYPE_ID' => 'LOGGING_ERROR',
            'MODULE_ID' => 'main',
            'DESCRIPTION' => "Ошибка логирования элемента {$elementId}: " . $el->LAST_ERROR
        ]);
    }
}
