<?php

    namespace Stepanov\App;

    use CIBlock;

    class MyIBlockHandler
    {
        /**
         * @param $arFields
         *
         * @return void
         * /*------------------------------------------------------------------------------------------#
         * Обработчик события на запрет корректировки текста или оценки отзыва
         * в инфоблоке "Отзывы" ('IBLOCK_CODE' = 'reviews') ↓
         * #------------------------------------------------------------------------------------------*/
        public static function notUpdateCommentAndRating(&$arFields): void
        {
            $currentIBlock =
                CIBlock::GetList([], ["ID" => $arFields["IBLOCK_ID"]])->GetNext(
                );
            if ($currentIBlock['CODE'] == 'reviews') {
                unset($arFields["PROPERTY_VALUES"]);
            }
        }
    }