<?php

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }
    /**
     * @var array                     $arParams
     * @var array                     $arResult
     * @var \CBitrixComponentTemplate $this
     * @var string                    $templateName
     * @var string                    $templateFile
     * @var string                    $templateFolder
     * @var string                    $componentPath
     * @var array                     $templateData
     * @var \CBitrixComponent         $component
     *
     * @global \CMain                 $APPLICATION
     * @global \CUser                 $USER
     * @global \CDatabase             $DB
     */

    $this->setFrameMode(true);
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
                            <?php
                                switch ($arItem["PROPERTY_RATING_VALUE"]):
                                    case 5: ?>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star--active"></span>
                                        <?php
                                        break; ?>
                                    <?php
                                    case 4: ?>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star"></span>
                                        <?php
                                        break; ?>
                                    <?php
                                    case 3: ?>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star"></span>
                                       <span class="component__rating-star"></span>
                                        <?php
                                        break; ?>
                                    <?php
                                    case 2: ?>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star"></span>
                                       <span class="component__rating-star"></span>
                                       <span class="component__rating-star"></span>
                                        <?php
                                        break; ?>
                                    <?php
                                    case 1: ?>
                                       <span class="component__rating-star--active"></span>
                                       <span class="component__rating-star"></span>
                                       <span class="component__rating-star"></span>
                                       <span class="component__rating-star"></span>
                                       <span class="component__rating-star"></span>
                                        <?php
                                        break; ?>
                                    <?php
                                    default: ?>
                                       <span class="component__rating-star"></span>
                                       <span class="component__rating-star"></span>
                                       <span class="component__rating-star"></span>
                                       <span class="component__rating-star"></span>
                                       <span class="component__rating-star"></span>
                                    <?php
                                endswitch; ?>
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
