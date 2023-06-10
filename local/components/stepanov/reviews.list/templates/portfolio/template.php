<?php

    use Bitrix\Main\Page\Asset;

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }
    /** @var array $arParams */
    /** @var array $arResult */
    /** @global \CMain $APPLICATION */
    /** @global \CUser $USER */
    /** @global \CDatabase $DB */
    /** @var \CBitrixComponentTemplate $this */
    /** @var string $templateName */
    /** @var string $templateFile */
    /** @var string $templateFolder */
    /** @var string $componentPath */
    /** @var array $templateData */
    /** @var \CBitrixComponent $component */
    $this->setFrameMode(true);
    Asset::getInstance()->addCss($templateFolder.'/css/reviewsStyle.css');
?>
<?php
    if ($arResult["IBLOCK"]): ?>
       <div class="component">
          <div id="test" class="component__container">
             <h2><?= $arResult["IBLOCK"]["NAME"] ?>:</h2>
              <?php
                  foreach ($arResult["ITEMS"] as $arItem): ?>
                     <div class="component__body">
                        <div class="component__username"><?= $arItem["PREVIEW_TEXT"] ?></div>
                        <div class="component__rating">
                           <span class="component__rating-count"><?= $arItem["PROPERTY_RATING_VALUE"] ?></span>
                           <span class="component__rating-star"></span>
                           <span class="component__rating-star"></span>
                           <span class="component__rating-star"></span>
                           <span class="component__rating-star"></span>
                           <span class="component__rating-star"></span>
                        </div>
                        <div class="component__comment"><?= $arItem["PROPERTY_COMMENT_VALUE"] ?></div>
                     </div>
                  <?php
                  endforeach; ?>
             <div class="component__nav-string"><?= $arResult["NAV_STRING"] ?></div>
              <?php
              ?>
             <div class="component__form-wrapper">
                <form class="component__form-body"
                      action="<?= POST_FORM_ACTION_URI ?>" method="post">
                    <?= bitrix_sessid_post() ?>
                   <label class="component__label-name" for="name">
                      Имя<input class="component__input-name" type="text"
                                name="name" id="name" required
                                placeholder="Введите имя*">
                   </label>
                   <div class="component__wrap-rating">
                       <?php
                           foreach ($arResult["RATING"] as $item): ?>
                              <input class="component__input-rating"
                                     type="radio" name="rating"
                                     id="rating<?= $item["ID"] ?>"
                                     value="<?= $item["ID"] ?>">
                              <label class="component__label-rating"
                                     for="rating<?= $item["ID"] ?>"></label>
                           <?php
                           endforeach; ?>
                   </div>

                   <label class="component__label-comment" for="comment">
                      Комментарий<textarea class="component__text-comment"
                                           name="comment" id="comment" required
                                           placeholder="Введите текст комментария*"></textarea>
                   </label>

                   <button class="component__submit-button" type="submit">
                      Оставить отзыв
                   </button>
                </form>
             </div>
          </div>
       </div>
    <?php
    endif; ?>
<script defer='defer'
        src="<?= $templateFolder ?>/js/jquery/jquery-3.7.0.min.js"></script>
<script defer='defer' src="<?= $templateFolder ?>/js/reviewsScript.js"></script>