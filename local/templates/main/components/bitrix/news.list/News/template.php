<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);


setlocale(LC_TIME, 'ru_RU.UTF-8');

?>

<?php if (!empty($arResult['ITEMS'])): ?>

    <div class="article-list">

        <? foreach ($arResult['ITEMS'] as $item): ?>
            <a class="article-item article-list__item" href="<?= $item['DETAIL_PAGE_URL'] ?>" data-anim="anim-3">
                <div class="article-item__background">
                    <img src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>" data-src="xxxHTMLLINKxxx0.39186223192351520.41491856731872767xxx" alt="">
                </div>
                <div class="article-item__wrapper">
                    <div class="article-item__title"><?= $item['NAME'] ?? '' ?></div>
                    <div class="article-item__content">
                        <?= $item['PREVIEW_TEXT'] ?? '' ?>
                    </div>
                </div>
            </a>




        <? endforeach ?>
    </div>





<? else: ?>

    <h2 style="width: 100%; height: 100%; text-align: center;">Нет новостей</h2>


<? endif ?>