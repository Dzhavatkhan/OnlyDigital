<?php

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;

function getIblockIdByCode (string $code)
{

    Loader::includeModule('iblock');
    $iblock = IblockTable::getList([
        'filter' => [
            "CODE" => $code,
        ],
        "select" => [
            "ID", 
            "CODE"
        ]
    ])->fetch();

    if(!isset($iblock['ID'])){
        throw new Exception("Не найден инфоблок с кодом $code");
    }

    return $iblock['ID'];

}



