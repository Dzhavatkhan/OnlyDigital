<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use CIBlock;
use CIBlockType;
use CIBlockProperty;
use CIBlockPropertyEnum;
use Sprint\Migration\Version as Version;

class Version20251208003817 extends Version
{
    public $iblockId;

    protected $author = "admin";

    protected $description = "vac";

    protected $moduleVersion = "5.6.0";

    public function up()
    {
        Loader::includeModule('iblock');
        $helper = $this->getHelperManager();
        $this->createIBlock();
        $this->createProperties();
    }

    public function down()
    {
        Loader::includeModule('iblock');

        // Удаляем инфоблок и все связанные данные
        if ($this->iblockId) {
            CIBlock::Delete($this->iblockId);
        }
    }


    protected function createIBlock()
    {
        $dbIblock = CIBlock::GetList([], [
            'CODE' => 'vacancies',
            'IBLOCK_TYPE_ID' => 'Content'
        ]);
        
        if ($existingIblock = $dbIblock->Fetch()) {
            $this->out("Инфоблок 'vacancies' уже существует с ID: " . $existingIblock['ID']);
            $this->iblockId = $existingIblock['ID'];
            return;
        }
        $arFields = [
            'ACTIVE' => 'Y',
            'NAME' => 'Вакансии',
            'CODE' => 'vacancies',
            'IBLOCK_TYPE_ID' => 'Content',
            'SITE_ID' => ['s1'],
            'SORT' => 100,
            'GROUP_ID' => ['2' => 'R'],
            'LIST_MODE' => 'S',
            'VERSION' => 2,
            'INDEX_ELEMENT' => 'Y',
            'INDEX_SECTION' => 'N',
            'WORKFLOW' => 'N',
            'BIZPROC' => 'N',
        ];


        $iblock = new CIBlock();
        $this->iblockId = $iblock->Add($arFields);
        if (!$this->iblockId) {
            throw new \Exception('Ошибка создания инфоблока: ' . $iblock->LAST_ERROR);
        }
    }


    protected function createProperties()
    {
        $properties = [
            [
                'NAME' => 'Офис',
                'CODE' => 'OFFICE',
                'PROPERTY_TYPE' => 'S',
                'SORT' => 100,
                'MULTIPLE' => 'N',
                'IS_REQUIRED' => 'N',
            ],
            [
                'NAME' => 'Локация',
                'CODE' => 'LOCATION',
                'PROPERTY_TYPE' => 'S',
                'SORT' => 110,
            ],
            [
                'NAME' => 'Требования',
                'CODE' => 'REQUIRE',
                'PROPERTY_TYPE' => 'S',
                'MULTIPLE' => 'Y',
                'SORT' => 120,
            ],

            [
                'NAME' => 'Направление деятельности',
                'CODE' => 'ACTIVITY',
                'PROPERTY_TYPE' => 'L',
                'SORT' => 130,
                'VALUES' => [
                    ['VALUE' => 'Производство', 'DEF' => 'N', 'SORT' => 100],
                    ['VALUE' => 'IT', 'DEF' => 'N', 'SORT' => 200],
                    ['VALUE' => 'Маркетинг', 'DEF' => 'N', 'SORT' => 300],
                ]
            ],
            [
                'NAME' => 'Тип занятости',
                'CODE' => 'TYPE',
                'PROPERTY_TYPE' => 'L',
                'SORT' => 140,
                'VALUES' => [
                    ['VALUE' => 'Полная занятость', 'DEF' => 'N', 'SORT' => 100],
                    ['VALUE' => 'Частичная занятость', 'DEF' => 'N', 'SORT' => 200],
                ]
            ],
            [
                'NAME' => 'Тип зарплаты',
                'CODE' => 'SALARY_TYPE',
                'PROPERTY_TYPE' => 'L',
                'SORT' => 150,
                'VALUES' => [
                    ['VALUE' => 'от', 'DEF' => 'N', 'SORT' => 100],
                    ['VALUE' => 'до', 'DEF' => 'N', 'SORT' => 200],
                    ['VALUE' => '=', 'DEF' => 'N', 'SORT' => 300],
                    ['VALUE' => 'договорная', 'DEF' => 'N', 'SORT' => 400],
                ]
            ],

            [
                'NAME' => 'Обязанности',
                'CODE' => 'DUTY',
                'PROPERTY_TYPE' => 'S',
                'USER_TYPE' => 'HTML',
                'SORT' => 160,
            ],
            [
                'NAME' => 'Условия',
                'CODE' => 'CONDITIONS',
                'PROPERTY_TYPE' => 'S',
                'USER_TYPE' => 'HTML',
                'SORT' => 170,
            ],

            [
                'NAME' => 'Зарплата',
                'CODE' => 'SALARY_VALUE',
                'PROPERTY_TYPE' => 'S',
                'SORT' => 180,
            ],
            [
                'NAME' => 'График работы',
                'CODE' => 'SCHEDULE',
                'PROPERTY_TYPE' => 'S',
                'SORT' => 190,
            ],
            [
                'NAME' => 'Сфера',
                'CODE' => 'FIELD',
                'PROPERTY_TYPE' => 'L',
                'SORT' => 200,
            ],
            [
                'NAME' => 'Email для откликов',
                'CODE' => 'EMAIL',
                'PROPERTY_TYPE' => 'S',
                'SORT' => 210,
            ],
        ];
        foreach ($properties as $property) {
            $values = isset($property['VALUES']) ? $property['VALUES'] : null;
            unset($property['VALUES']);

            $property['IBLOCK_ID'] = $this->iblockId;

            $ibProperty = new CIBlockProperty();
            $propertyId = $ibProperty->Add($property);

            foreach ($values as $value) {
                CIBlockPropertyEnum::Add([
                    "PROPERTY_ID" => $propertyId,
                    "VALUE" => $value['VALUE'],
                    "DEF" => $value['DEF'],
                    "SORT" => $value['SORT']
                ]);
            }
        }
    }
}
