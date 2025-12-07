<?php

use Bitrix\Main\Loader;

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}
Loader::includeModule('iblock');
echo "hello amigo \n";


$IBLOCK_ID = 2;
$arProps = [];
$vacancyCsvFile = $_SERVER['DOCUMENT_ROOT'] . "/local/parser/vacancy.csv";


$rowRead = 1;
$el = new CIBlockElement();

$rsPropValues = CIBlockPropertyEnum::GetList(
    ["SORT" => "ASC", "VALUE" => "ASC"],
    ['IBLOCK_ID' => $IBLOCK_ID]
);

while ($arProp = $rsPropValues->Fetch()) {
    $key = $arProp['VALUE'];
    $arProps[$arProp['PROPERTY_CODE']][$key] = $arProp['ID'];
}

$rsElements = CIBlockElement::GetList([], ['IBLOCK_ID' => $IBLOCK_ID], false, false, ['ID']);
while ($element = $rsElements->GetNext()) {
    CIBlockElement::Delete($element['ID']);
}


if (($handle = fopen($vacancyCsvFile, 'r')) !== false) {

    while (($data = fgetcsv($handle, 1000, ',')) !== false) {

        if ($rowRead == 1) {
            $rowRead++;
            continue;
        }
        $PROP = [
            'ACTIVITY' => $data[9] ?? '',
            'FIELD' => $data[11] ?? '',
            'OFFICE' => $data[1] ?? '',
            'LOCATION' => $data[2] ?? '',
            'REQUIRE' => $data[4] ?? '',
            'DUTY' => $data[5] ?? '',
            'CONDITIONS' => $data[6] ?? '',
            'EMAIL' => $data[12] ?? '',
            'DATE' => date('d.m.Y'),
            'TYPE' => $data[8] ?? '',
            'SALARY_TYPE' => '',
            'SALARY_VALUE' => $data[7] ?? '',
            'SCHEDULE' => $data[10] ?? '',
        ];

        foreach ($PROP as $key => &$value) {
            $value = trim($value);
            $value = str_replace("\n", "", $value);
            if (stripos($value, '•') !== false) {
                $value = explode('•', $value);
                array_splice($value, 0, 1);
                foreach ($value as &$str) {
                    $str = trim($str);
                }
            } elseif (isset($arProps[$key]) && !empty($value)) {
                foreach ($arProps[$key] as $propKey => $propVal) {
                    if (
                        stripos($propKey, $value) !== false ||
                        stripos($value, $propKey) !== false
                    ) {
                        $value = $propVal;
                        break;
                    }
                }
            }
        }

        if ($PROP['SALARY_VALUE'] == '-' || empty($PROP['SALARY_VALUE'])) {
            $PROP['SALARY_VALUE'] = '';
            $PROP['SALARY_TYPE'] = '';
        } elseif (stripos($PROP['SALARY_VALUE'], 'по договоренности') !== false) {
            $PROP['SALARY_VALUE'] = '';
            $PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE']['договорная'] ?? '';
        } else {
            $arSalary = explode(' ', $PROP['SALARY_VALUE']);
            if ($arSalary[0] == 'от' || $arSalary[0] == 'до') {
                $PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE'][$arSalary[0]] ?? '';
                array_splice($arSalary, 0, 1);
                $PROP['SALARY_VALUE'] = implode(' ', $arSalary);
            } else {
                $PROP['SALARY_TYPE'] = $arProps['SALARY_TYPE']['='] ?? '';
            }
        }

        $arLoadProductArray = [
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => $IBLOCK_ID,
            "PROPERTY_VALUES" => $PROP,
            "NAME" => trim($data[3] ?? 'Без названия'),
            "ACTIVE" => !empty(end($data)) ? 'Y' : 'N',
            "PREVIEW_TEXT" => $data[3] ?? '',
            "DETAIL_TEXT" => "Импортировано из CSV " . date('d.m.Y H:i:s'),
        ];
        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            echo "Добавлена вакансия: " . ($data[3] ?? '') . " (ID: $PRODUCT_ID)<br>";
        } else {
            echo "Ошибка: " . $el->LAST_ERROR . "<br>";
            echo "Данные: " . print_r($arLoadProductArray, true) . "<br>";
        }        
    }

    fclose($handle);
}
else{
    echo "\n open csv file amigo!";
}
