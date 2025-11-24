<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arResult
 */
?>

<div class="contact-form">
    <div class="contact-form__head">
        <div class="contact-form__head-title"><?=$arResult["FORM_TITLE"] ?? 'Связаться'?></div>
        <?if ($arResult["isFormDescription"] == "Y" && !empty($arResult["FORM_DESCRIPTION"])):?>
        <div class="contact-form__head-text"><?=$arResult["FORM_DESCRIPTION"]?></div>
        <?else:?>
        <div class="contact-form__head-text">Наши сотрудники помогут выполнить подбор услуги и&nbsp;расчет цены с&nbsp;учетом ваших требований</div>
        <?endif;?>
    </div>

    <?if ($arResult["isFormErrors"] == "Y"):?>
    <div class="error-message"><?=$arResult["FORM_ERRORS_TEXT"];?></div>
    <?endif;?>

    <?if (!empty($arResult["FORM_NOTE"])):?>
    <div class="success-message"><?=$arResult["FORM_NOTE"]?></div>
    <?endif;?>

    <?if ($arResult["isFormNote"] != "Y"):?>
    <?=$arResult["FORM_HEADER"]?>
    
    <form class="contact-form__form" action="<?=POST_FORM_ACTION_URI?>" method="POST">
        <div class="contact-form__form-wrapper">
            <div class="contact-form__form-inputs">
                <?
                // Предопределенные настройки полей для соответствия верстке
                $fieldSettings = [
                    'text' => [
                        'notification' => 'Поле должно содержать не менее 3-х символов'
                    ],
                    'email' => [
                        'notification' => 'Неверный формат почты'
                    ],
                    'tel' => [
                        'notification' => '',
                        'attributes' => 'data-inputmask="\'mask\': \'+79999999999\', \'clearIncomplete\': \'true\'" maxlength="12" x-autocompletetype="phone-full"'
                    ]
                ];
                
                // Обрабатываем поля в нужном порядке
                $processedFields = [];
                
                // Сначала обрабатываем текстовые поля
                foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion):
                    if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden') {
                        echo $arQuestion["HTML_CODE"];
                        $processedFields[] = $FIELD_SID;
                        continue;
                    }
                    
                    // Пропускаем textarea - обработаем отдельно
                    if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'textarea') {
                        continue;
                    }
                    
                    $fieldType = $arQuestion['STRUCTURE'][0]['FIELD_TYPE'];
                    $fieldId = $arQuestion['STRUCTURE'][0]['ID'];
                    $isRequired = $arQuestion["REQUIRED"] == "Y";
                    $hasError = isset($arResult["FORM_ERRORS"][$FIELD_SID]);
                    $fieldValue = htmlspecialcharsbx($arResult["arrVALUES"][$FIELD_SID] ?? '');
                    
                    // Определяем тип поля для верстки
                    $inputType = 'text';
                    $fieldHtmlId = 'field_' . $FIELD_SID;
                    $fieldLabel = $arQuestion["CAPTION"];
                    
                    // Автоматическое определение типа по содержимому
                    if (stripos($FIELD_SID, 'email') !== false || stripos($fieldLabel, 'email') !== false) {
                        $inputType = 'email';
                        $fieldHtmlId = 'medicine_email';
                    } elseif (stripos($FIELD_SID, 'phone') !== false || stripos($fieldLabel, 'телефон') !== false || stripos($fieldLabel, 'phone') !== false) {
                        $inputType = 'tel';
                        $fieldHtmlId = 'medicine_phone';
                    } elseif (stripos($FIELD_SID, 'name') !== false || stripos($fieldLabel, 'имя') !== false || stripos($fieldLabel, 'name') !== false) {
                        $fieldHtmlId = 'medicine_name';
                    } elseif (stripos($FIELD_SID, 'company') !== false || stripos($fieldLabel, 'компания') !== false || stripos($fieldLabel, 'company') !== false) {
                        $fieldHtmlId = 'medicine_company';
                    } else {
                        $fieldHtmlId = $FIELD_SID;
                    }
                    
                    // Формируем имя поля в стандартном формате Bitrix
                    $fieldName = "form_{$fieldType}_{$fieldId}";
                ?>
                
                <div class="input contact-form__input <?=$hasError ? 'input--error' : ''?>">
                    <label class="input__label" for="<?=$fieldHtmlId?>">
                        <div class="input__label-text">
                            <?=$fieldLabel?><?if ($isRequired):?>*<?endif;?>
                        </div>
                        
                        <?if ($inputType == 'tel'): ?>
                            <input class="input__input" type="<?=$inputType?>" id="<?=$fieldHtmlId?>" 
                                   name="<?=$fieldName?>" value="<?=$fieldValue?>" 
                                   <?if ($isRequired):?>required=""<?endif;?>
                                   data-inputmask="'mask': '+79999999999', 'clearIncomplete': 'true'" 
                                   maxlength="12" x-autocompletetype="phone-full">
                        <?else: ?>
                            <input class="input__input" type="<?=$inputType?>" id="<?=$fieldHtmlId?>" 
                                   name="<?=$fieldName?>" value="<?=$fieldValue?>" 
                                   <?if ($isRequired):?>required=""<?endif;?>>
                        <?endif; ?>
                        
                        <div class="input__notification">
                            <?if ($hasError):?>
                                <?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?>
                            <?else:?>
                                <?=$fieldSettings[$inputType]['notification'] ?? ''?>
                            <?endif;?>
                        </div>
                    </label>
                </div>
                <?
                    $processedFields[] = $FIELD_SID;
                endforeach;
                ?>
            </div>

            <!-- Textarea в правой части -->
            <?
            $textareaField = null;
            foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) {
                if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'textarea' && !in_array($FIELD_SID, $processedFields)) {
                    $textareaField = $arQuestion;
                    $textareaSid = $FIELD_SID;
                    $textareaId = $arQuestion['STRUCTURE'][0]['ID'];
                    break;
                }
            }
            
            if ($textareaField):
                $hasError = isset($arResult["FORM_ERRORS"][$textareaSid]);
                $fieldValue = htmlspecialcharsbx($arResult["arrVALUES"][$textareaSid] ?? '');
                $textareaName = "form_textarea_{$textareaId}";
            ?>
            <div class="contact-form__form-message">
                <div class="input <?=$hasError ? 'input--error' : ''?>">
                    <label class="input__label" for="medicine_message">
                        <div class="input__label-text"><?=$textareaField["CAPTION"]?></div>
                        <textarea class="input__input" id="medicine_message" name="<?=$textareaName?>"><?=$fieldValue?></textarea>
                        <div class="input__notification">
                            <?if ($hasError):?>
                                <?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$textareaSid])?>
                            <?endif;?>
                        </div>
                    </label>
                </div>
            </div>
            <?endif;?>
        </div>

        <?if($arResult["isUseCaptcha"] == "Y"):?>
        <div class="contact-form__captcha">
            <div class="input">
                <label class="input__label">
                    <div class="input__label-text"><?=GetMessage("FORM_CAPTCHA_FIELD_TITLE")?>*</div>
                    <input type="text" name="captcha_word" size="30" maxlength="50" value="" class="input__input" required />
                    <input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" />
                    <div class="captcha-image">
                        <img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" width="180" height="40" alt="CAPTCHA"/>
                    </div>
                </label>
            </div>
        </div>
        <?endif;?>

        <div class="contact-form__bottom">
            <div class="contact-form__bottom-policy">
                Нажимая &laquo;Отправить&raquo;, Вы&nbsp;подтверждаете, что ознакомлены, полностью согласны и&nbsp;принимаете условия &laquo;Согласия на&nbsp;обработку персональных данных&raquo;.
            </div>
            
            <button class="form-button contact-form__bottom-button" type="submit" name="web_form_submit" 
                    data-success="Отправлено" data-error="Ошибка отправки">
                <div class="form-button__title">
                    <?=htmlspecialcharsbx(trim($arResult["arForm"]["BUTTON"]) == '' ? 'Оставить заявку' : $arResult["arForm"]["BUTTON"]);?>
                </div>
            </button>

            <?if ($arResult["F_RIGHT"] >= 15):?>
            <input type="hidden" name="web_form_apply" value="Y" />
            <?endif;?>
        </div>
        
        <?=$arResult["FORM_FOOTER"]?>
    </form>
    <?endif;?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('medicine_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('7')) {
                value = '+' + value;
            } else if (value.startsWith('8')) {
                value = '+7' + value.substring(1);
            } else if (value) {
                value = '+7' + value;
            }
            e.target.value = value.substring(0, 12);
        });
    }
});
</script>

<style>
    .contact-form__form-wrapper{
        display: flex;
        justify-content: space-between;
    }
</style>