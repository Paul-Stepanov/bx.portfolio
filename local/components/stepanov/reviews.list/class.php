<?php

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }

    use Bitrix\Main\{Loader, LoaderException, SystemException};
    use Bitrix\Main\Engine\Contract\Controllerable;

    /**------------------------------------------------------------------------#
     * Для использования компонента необходимо создать информационный блок
     * "Отзывы", с символьным кодом: "reviews", содержащий свойства:
     * - "Комментарий" (тип - строка, код свойства - "COMMENT", обязательное к
     * заполнению);
     * - "Оценка" (тип - список, код свойства - "RATING", значения списка от 1
     * до 5). Для хранения имени комментатора используется поле инфоблока
     * "Анонс" ("PREVIEW_TEXT"), текста комментария - свойство "Комментарий"
     * ("COMMENT"), оценки - свойство "Оценка" ("RATING").
     * #----------------------------------------------------------------------*/
    class ReviewsList extends CBitrixComponent
        implements Controllerable
    {
        /**
         * Счетчик компонентов.
         *
         * @var int
         */
        private static int $countReviewsComponent = 0;

        /**
         * Данные информационного блока "отзывы".
         *
         * @var array|bool
         */
        private array|bool $iBlockReviews;

        /**
         * Массив значений для осуществления транслитерации символьного кода
         * элемента.
         *
         * @var array|string[]
         */
        private array $translitCodeParams = [
            "max_len" => "100",
            // обрезает символьный код до 100 символов
            "change_case" => "L",
            // буквы преобразуются к нижнему регистру
            "replace_space" => "_",
            // меняем пробелы на нижнее подчеркивание
            "replace_other" => "_",
            // меняем левые символы на нижнее подчеркивание
            "delete_repeat_replace" => "true",
            // удаляем повторяющиеся нижние подчеркивания
        ];

        /**
         * Массив полей заносимых в информационный блок при создании элемента
         * комментария.
         *
         * @var array
         */
        private array $commentFieldsIBlockReviews = [
            "ACTIVE" => "Y",
            "IBLOCK_ID" => "",
            "NAME" => "",
            "PREVIEW_TEXT" => "",
            "CODE" => "",
            "PROPERTY_VALUES" => [
                "RATING" => "",
                "COMMENT" => "",
            ],
        ];

        /**
         *---------------------------------------------------------------------#
         * Подготовка параметров компонента ↓
         * ------------------------------------------------------------------#
         *
         * @param $arParams
         *
         * @return array
         *
         */
        public function onPrepareComponentParams($arParams): array
        {
            $this->checkIBlockModule();

            $this->getIBlockReviews();

            $arParams["REVIEWS_COUNT"] = intval($arParams["REVIEWS_COUNT"]);

            if ($arParams["REVIEWS_COUNT"] <= 0) {
                $arParams["REVIEWS_COUNT"] = 6;
            }

            return $arParams;
        }

        /**
         *---------------------------------------------------------------------#
         * Проверка наличие в системе установленного модуля "Информационные
         * блоки" ↓
         *-------------------------------------------------------------------#
         */
        private function checkIBlockModule(): void
        {
            try {
                Loader::IncludeModule("iblock")
                    ? true
                    : throw new LoaderException(
                    ShowError(
                        "Не установлен модуль 'Информационные блоки (iblock)'!!!",
                    )
                );
            } catch (LoaderException $exception) {
                $exception->getMessage();
            }
        }

        /**
         *---------------------------------------------------------------------#
         * Запрос данных информационного блока "Отзывы" ↓
         *-------------------------------------------------------------------#
         *
         * @return void
         */
        private function getIBlockReviews(): void
        {
            $this->iBlockReviews = CIBlock::GetList(
                arOrder: [],
                arFilter: ["CODE" => "reviews"],
            )
                ->GetNext();
        }

        /**
         *---------------------------------------------------------------------#
         * Формирование массива arResult и передача его в шаблон компонента ↓
         * ------------------------------------------------------------------#
         *
         * @return void
         *
         */
        public function executeComponent(): void
        {
            $this->checkIBlockReviews($this->iBlockReviews);

            $iBlockReviewsResultObject = $this->getIBlockReviewsResultObject();

            $this->getIBlockReviewsElements($iBlockReviewsResultObject);

            $this->getNavString($iBlockReviewsResultObject);

            $this->getRatingEnumFields();

            $this->arResult["IBLOCK"] = $this->iBlockReviews;

            $this->arResult["COUNT"] = self::$countReviewsComponent++;

            $this->includeComponentTemplate();
        }

        /**
         * --------------------------------------------------------------------#
         * Проверка существования инфоблока "Отзывы", с символьным кодом
         * "reviews" ↓
         * ------------------------------------------------------------------#
         *
         * @param $iBlockReviews
         *
         */
        private function checkIBlockReviews($iBlockReviews)
        {
            try {
                $iBlockReviews
                    ? true
                    : throw new SystemException(
                    ShowError(
                        "Отсутствует информационный блок 'Отзывы'!
                                Для использования компонента необходимо создать информационный блок 'Отзывы',
                                с символьным кодом: 'reviews', содержащий свойства:
                                - 'Комментарий' (тип - строка, код свойства - 'COMMENT', обязательное к заполнению);
                                - 'Оценка' (тип - список, код свойства - 'RATING', значения списка от 1 до 5).",
                    )
                );
            } catch (SystemException $exception) {
                $exception->getMessage();
            }
        }

        /**
         *---------------------------------------------------------------------#
         * Создание экземпляра класса CIBlockResult ↓
         * ------------------------------------------------------------------#
         *
         * @return \CIBlockResult|int
         */
        private function getIBlockReviewsResultObject(): CIBlockResult|int
        {
            return CIBlockElement::GetList(
                arOrder: [$this->arParams["SORT_BY"] => $this->arParams["SORT_ORDER"]],
                arFilter: [$this->iBlockReviews["ID"]],
                arGroupBy: false,
                arNavStartParams: ["nPageSize" => $this->arParams["REVIEWS_COUNT"]],
                arSelectFields: [
                    "ID",
                    "IBLOCK_ID",
                    "IBLOCK_NAME",
                    "NAME",
                    "PREVIEW_TEXT",
                    "DATE_ACTIVE_FROM",
                    "PROPERTY_COMMENT",
                    "PROPERTY_RATING",
                ],
            );
        }

        /**
         *---------------------------------------------------------------------#
         * Выбираем элементы инфоблока "Отзывы" с необходимыми полями ↓
         *------------------------------------------------------------------#
         *
         * @param  CIBlockResult  $iBlockResult
         *
         * @return void
         */
        private function getIBlockReviewsElements(
            CIBlockResult $iBlockResult,
        ): void {
            while ($element = $iBlockResult->GetNextElement()) {
                $id = $element->fields["ID"];
                $this->arResult["ITEMS"][$id] = $element->GetFields();
            }
        }

        /**
         *---------------------------------------------------------------------#
         * Формирование постраничной навигации ↓
         *------------------------------------------------------------------#
         *
         * @param  CIBlockResult  $iBlockResult
         *
         * @return void
         */
        private function getNavString(CIBlockResult $iBlockResult): void
        {
            $this->arResult["NAV_STRING"] =
                $iBlockResult->GetPageNavStringEx(
                    navComponentObject: $navComponentObject,
                    navigationTitle: '',
                    templateName: "modern",
                );
        }

        /**
         *---------------------------------------------------------------------#
         * Формирование списка полей элементов рейтинга ↓
         *------------------------------------------------------------------#
         *
         * @return void
         */
        private function getRatingEnumFields(): void
        {
            $ratingEnums = CIBlockPropertyEnum::GetList(
                ["DEF" => "DESC", "SORT" => "DESC"],
                ["IBLOCK_ID" => $this->iBlockReviews["ID"], "CODE" => "RATING"],
            );

            while ($enum_fields = $ratingEnums->GetNext()) {
                $value = $enum_fields["VALUE"];
                $this->arResult["RATING"][$value] = $enum_fields;
            }
        }

        /**
         *---------------------------------------------------------------------#
         * Пред проверка параметров для AJAX  ↓
         *-------------------------------------------------------------------#
         *
         * @return array[]
         */
        public function configureActions(): array
        {
            return [
                'ajaxAddComment' => [
                    'prefilters' => [],
                ],
            ];
        }

        /**
         *---------------------------------------------------------------------#
         * Добавление комментария с помощью AJAX ↓
         * ------------------------------------------------------------------#
         *
         * @param $formData
         *
         * @throws \Bitrix\Main\SystemException
         */
        public function ajaxAddCommentAction($formData)
        {
            $comment = $this->preparationCommentData($formData);

            $this->checkCompletionCommentData($comment);

            $this->preparationCommentBody($comment);

            $this->addComment();
        }

        /**
         * --------------------------------------------------------------------#
         * Подготовка данных из формы, полученных посредством AJAX ↓
         * ------------------------------------------------------------------#
         *
         * @param $commentData
         *
         * @return mixed
         */
        private function preparationCommentData($commentData): mixed
        {
            $commentBody = json_decode($commentData, true);

            $commentBody = $this->clearingCommentData($commentBody);

            if ($commentBody["rating"] == 'default') {
                $commentBody["rating"] = 0;
            }

            return $commentBody;
        }

        /**
         *---------------------------------------------------------------------#
         * Очистка данных полученных из формы ↓
         * ------------------------------------------------------------------#
         *
         * @param $commentData
         *
         * @return mixed
         */
        private function clearingCommentData($commentData): mixed
        {
            foreach ($commentData as $key => $value) {
                $commentData[$key] = htmlspecialchars(
                    stripslashes(
                        trim(
                            $value,
                        ),
                    ),
                );
            }

            return $commentData;
        }

        /**
         * --------------------------------------------------------------------#
         * Проверка данных формы на заполнение ↓
         * -----------------------------------------------------------------#
         *
         * @param $commentData
         *
         * @return void
         * @throws \Bitrix\Main\SystemException
         */
        private function checkCompletionCommentData(
            $commentData,
        ): void {
            if ((empty($commentData['name']))
                || (empty($commentData['comment']))
            ) {
                throw new SystemException(
                    "Поле 'имя' и 'комментарий' обязательны к заполнению!"
                );
            }
        }

        /**
         * --------------------------------------------------------------------#
         * Подготовка полей для добавления нового комментария  ↓
         * ------------------------------------------------------------------#
         *
         * @param $commentData
         *
         * @return void
         */
        private function preparationCommentBody($commentData): void
        {
            $this->commentFieldsIBlockReviews["IBLOCK_ID"] =
                $this->iBlockReviews["ID"];

            $this->commentFieldsIBlockReviews["PREVIEW_TEXT"] =
                $commentData["name"];

            $this->commentFieldsIBlockReviews["NAME"] =
                sprintf("%s_%s", $commentData["name"], rand());

            $this->commentFieldsIBlockReviews["CODE"] = $this->translitData(
                $this->commentFieldsIBlockReviews["NAME"],
            );

            $this->commentFieldsIBlockReviews["PROPERTY_VALUES"]["RATING"] =
                $commentData["rating"];

            $this->commentFieldsIBlockReviews["PROPERTY_VALUES"]["COMMENT"] =
                $commentData["comment"];
        }

        /**
         * --------------------------------------------------------------------#
         * Метод для транслитации строки ↓
         * ------------------------------------------------------------------#
         *
         * @param $data
         *
         * @return string
         */
        private function translitData($data): string
        {
            return CUtil::translit(
                $data,
                "ru",
                $this->translitCodeParams,
            );
        }

        /**
         * --------------------------------------------------------------------#
         * Добавление комментария в информационный блок ↓
         * -----------------------------------------------------------------#
         *
         * @return void
         */
        private function addComment(): void
        {
            try {
                $oElement = new CIBlockElement();

                $commentId = $oElement->Add($this->commentFieldsIBlockReviews);

                if ($commentId) {
                    return;
                } else {
                    throw new SystemException(
                        "Ошибка при добавлении комментария!"
                    );
                }
            } catch (SystemException $exception) {
                $exception->getMessage();
            }
        }
    }
