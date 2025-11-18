<?php

if (!empty($arResult['ITEMS'])) {
    // Получаем ID всех элементов
    $elementIds = array();
    foreach ($arResult['ITEMS'] as $arItem) {
        $elementIds[] = $arItem['ID'];
    }

    // Получаем все разделы для всех элементов
    $dbSections = CIBlockElement::GetElementGroups($elementIds, true);
    $elementSections = array();

    while ($arSection = $dbSections->Fetch()) {
        $elementId = $arSection['IBLOCK_ELEMENT_ID'];
        if (!isset($elementSections[$elementId])) {
            $elementSections[$elementId] = array();
        }
        $elementSections[$elementId][] = $arSection;
    }

    // Добавляем информацию о разделах к каждому элементу
    foreach ($arResult['ITEMS'] as $key => $arItem) {
        $elementId = $arItem['ID'];

        if (isset($elementSections[$elementId]) && !empty($elementSections[$elementId])) {
            // Берем первый раздел
            $firstSection = $elementSections[$elementId][0];
            $arResult['ITEMS'][$key]['SECTION_NAME'] = $firstSection['NAME'];
            $arResult['ITEMS'][$key]['SECTION_URL'] = $firstSection['SECTION_PAGE_URL'];
            $arResult['ITEMS'][$key]['SECTION_CODE'] = $firstSection['CODE']; // Добавляем CODE раздела

            // Также сохраняем коды всех разделов элемента для фильтрации
            $sectionCodes = array();
            foreach ($elementSections[$elementId] as $section) {
                if (!empty($section['CODE'])) {
                    $sectionCodes[] = $section['CODE'];
                }
            }
            $arResult['ITEMS'][$key]['SECTIONS_CODES'] = implode(' ', $sectionCodes);
        } else {
            $arResult['ITEMS'][$key]['SECTION_NAME'] = "Без раздела";
            $arResult['ITEMS'][$key]['SECTION_URL'] = "";
            $arResult['ITEMS'][$key]['SECTION_CODE'] = "";
            $arResult['ITEMS'][$key]['SECTIONS_CODES'] = "";
        }
    }
}
if(!empty($arResult['PROPERTIES']['gallery']['VALUE'])){
    foreach ($arResult['PROPERTIES']['gallery']['VALUE'] as $key => $photoId) {
        $arPhoto = CFile::GetFileArray($photoId);
        $arResult['PROPERTIES']['photos'][$key]['src'] = $arPhoto['SRC'];
    }
}