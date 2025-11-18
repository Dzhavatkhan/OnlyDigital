<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

?>

<? if (!empty($arResult['ID'])): ?>
        <div id="barba-wrapper">
            <div class="article-detail">
                <a class="article-detail__back" href="/" data-anim="anim-3">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                    Назад к новостям
                </a>
                
                <div class="article-detail__content">
                    <div class="article-detail__background">
                        <img src="<?= $arResult['PREVIEW_PICTURE']['SRC']?>" data-src="xxxHTMLLINKxxx0.39186223192351520.41491856731872767xxx" alt="">
                    </div>
                    
                    <div class="article-detail__wrapper">
                        <div class="article-detail__title"><?= $arResult['NAME'] ?></div>
                        <div class="article-detail__description">
                            <?= $arResult['PREVIEW_TEXT']?>
                        </div>
                        
                        <div class="article-detail__meta">
                            <div class="article-detail__date">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/>
                                    <path d="M13 7h-2v6h6v-2h-4z"/>
                                </svg>
                                <?= $arResult['DATE']?>
                            </div>
                            
                            <div class="article-detail__share">
                                <span>Поделиться:</span>
                                <a href="#" class="share-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                <a href="#" class="share-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                

            </div>
        </div>
        <? endif;?>
        <style>
            .article-detail {
                padding: 40px 20px;
                max-width: 1200px;
                margin: 0 auto;
            }
            
            .article-detail__back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                color: #666;
                text-decoration: none;
                margin-bottom: 30px;
                padding: 8px 16px;
                border-radius: 6px;
                transition: all 0.3s ease;
            }
            
            .article-detail__back:hover {
                background: #f0f0f0;
                color: #333;
            }
            
            .article-detail__content {
                position: relative;
                border-radius: 12px;
                overflow: hidden;
                margin-bottom: 60px;
            }
            
            .article-detail__background {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            }
            
            .article-detail__background img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .article-detail__wrapper {
                position: relative;
                background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.7) 100%);
                color: white;
                padding: 60px 40px;
                min-height: 500px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            
            .article-detail__title {
                font-size: 3rem;
                font-weight: 700;
                line-height: 1.2;
                margin-bottom: 30px;
            }
            
            .article-detail__description {
                font-size: 1.3rem;
                line-height: 1.6;
                margin-bottom: 40px;
                max-width: 800px;
            }
            
            .article-detail__meta {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 20px;
            }
            
            .article-detail__date {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 1rem;
                opacity: 0.9;
            }
            
            .article-detail__share {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            
            .share-btn {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: rgba(255,255,255,0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: white;
                transition: all 0.3s ease;
            }
            
            .share-btn:hover {
                background: rgba(255,255,255,0.3);
                transform: translateY(-2px);
            }
            
            .article-detail__navigation {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }
            
            .article-nav {
                display: flex;
                align-items: center;
                gap: 20px;
                padding: 30px;
                background: #f8f9fa;
                border-radius: 12px;
                text-decoration: none;
                color: #333;
                transition: all 0.3s ease;
            }
            
            .article-nav:hover {
                background: #e9ecef;
                transform: translateY(-2px);
            }
            
            .article-nav--prev {
                text-align: left;
            }
            
            .article-nav--next {
                text-align: right;
                flex-direction: row-reverse;
            }
            
            .article-nav__arrow {
                flex-shrink: 0;
            }
            
            .article-nav__label {
                font-size: 0.9rem;
                color: #666;
                margin-bottom: 5px;
            }
            
            .article-nav__title {
                font-size: 1.1rem;
                font-weight: 600;
                line-height: 1.3;
            }
            
            @media (max-width: 768px) {
                .article-detail__title {
                    font-size: 2rem;
                }
                
                .article-detail__description {
                    font-size: 1.1rem;
                }
                
                .article-detail__wrapper {
                    padding: 40px 20px;
                    min-height: 400px;
                }
                
                .article-detail__meta {
                    flex-direction: column;
                    align-items: flex-start;
                }
                
                .article-detail__navigation {
                    grid-template-columns: 1fr;
                }
                
                .article-nav {
                    padding: 20px;
                }
            }
        </style>
    </body>
</html>