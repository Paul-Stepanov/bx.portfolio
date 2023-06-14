<?php

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }

    use Bitrix\Main\{Application,
        HttpRequest,
        Loader,
        LoaderException,
        SystemException
    };

    /**------------------------------------------------------------------------------------------#
     * Для использования компонента необходимо создать информационный блок
     * "Отзывы", с символьным кодом: "reviews", содержащий свойства:
     * - "Комментарий" (тип - строка, код свойства - "COMMENT", обязательное к
     * заполнению);
     * - "Оценка" (тип - список, код свойства - "RATING", значения списка от 1
     * до 5). Для хранения имени комментатора используется поле инфоблока
     * "Анонс" ("PREVIEW_TEXT"), текста комментария - свойство "Комментарий"
     * ("COMMENT"), оценки - свойство "Оценка" ("RATING").
     * #------------------------------------------------------------------------------------------*/
    class ReviewsList extends CBitrixComponent
    {
        /**
         * Постраничная навигация.
         *
         * @var string|false
         */
        private string|false $navString;

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
         *------------------------------------------------------------------------------------------#
         * Подготовка параметров компонента ↓
         * ---------------------------------------------------------------------------------------#
         *
         * @param $arParams
         *
         * @return array
         *
         */
        public function onPrepareComponentParams($arParams): array
        {
            $arParams["REVIEWS_COUNT"] = intval($arParams["REVIEWS_COUNT"]);

            if ($arParams["REVIEWS_COUNT"] <= 0) {
                $arParams["REVIEWS_COUNT"] = 6;
            }

            return $arParams;
        }

        /**
         *------------------------------------------------------------------------------------------#
         * Формирование массива arResult и передача его в шаблон компонента ↓
         * ---------------------------------------------------------------------------------------#
         *
         * @return void
         *
         */
        public function executeComponent(): void
        {
            $this->checkIBlockModule();

            $this->getIBlockReviews();

            $this->checkIBlockReviews($this->iBlockReviews);

            $iBlockReviewsResultObject = $this->getIBlockReviewsResultObject();

            $this->getIBlockReviewsElements($iBlockReviewsResultObject);

            $this->getNavString($iBlockReviewsResultObject);

            $this->getRatingEnumFields();

            $this->addComment($this->getCommentRequestObject());

            $this->arResult["IBLOCK"] = $this->iBlockReviews;

            $this->includeComponentTemplate();
        }

        /**
         *------------------------------------------------------------------------------------------#
         * Проверка наличие в системе установленного модуля "Информационные
         * блоки" ↓
         *----------------------------------------------------------------------------------------#
         */
        private function checkIBlockModule(): void
        {
            try {
                Loader::IncludeModule("iblock")
                    ? true
                    : throw new LoaderException(
                    ShowError(
                        strError: "Не установлен модуль 'Информационные блоки (iblock)'!!!",
                    )
                );
            } catch (LoaderException $exception) {
                $exception->getMessage();
            }
        }

        /**
         *------------------------------------------------------------------------------------------#
         * Запрос данных информационного блока "Отзывы" ↓
         *----------------------------------------------------------------------------------------#
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
         * ------------------------------------------------------------------------------------------#
         * Проверка существования инфоблока "Отзывы", с символьным кодом
         * "reviews" ↓
         * ----------------------------------------------------------------------------------------#
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
                        strError: "Отсутствует информационный блок 'Отзывы'!
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
         *------------------------------------------------------------------------------------------#
         * Получаем экземпляр класса CIBlockResult ↓
         * ---------------------------------------------------------------------------------------#
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
         *------------------------------------------------------------------------------------------#
         * Выбираем элементы инфоблока "Отзывы" с необходимыми полями ↓
         *---------------------------------------------------------------------------------------#
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
         *------------------------------------------------------------------------------------------#
         * Формирование постраничной навигации ↓
         *---------------------------------------------------------------------------------------#
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
         *------------------------------------------------------------------------------------------#
         * Формирование списка полей элементов рейтинга ↓
         *---------------------------------------------------------------------------------------#
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
         *------------------------------------------------------------------------------------------#
         * Добавляем комментарий в инфоблок "Отзывы" ↓
         * ---------------------------------------------------------------------------------------#
         *
         * @param  HttpRequest  $request
         *
         * @return void
         */
        private function addComment(
            HttpRequest $request,
        ): void {
            $values = $request->getPostList()->toArray();

            if (!empty($values)) {
                if (!empty($values["name"] && !empty($values["comment"]))) {
                    $values["name"] = $this->checkFormData($values['name']);
                    $values["comment"] =
                        $this->checkFormData($values['comment']);
                    $this->commentFieldsIBlockReviews["IBLOCK_ID"] =
                        $this->iBlockReviews["ID"];
                    $this->commentFieldsIBlockReviews["PREVIEW_TEXT"] =
                        $values["name"];
                    $values["name"] = sprintf("%s_%s", $values["name"], rand());
                    $this->commentFieldsIBlockReviews["NAME"] = $values["name"];
                    $code = CUtil::translit(
                        $values['name'],
                        "ru",
                        $this->translitCodeParams,
                    );
                    $this->commentFieldsIBlockReviews["CODE"] = $code;
                    $this->commentFieldsIBlockReviews["PROPERTY_VALUES"]["RATING"] =
                        $values["rating"];
                    $this->commentFieldsIBlockReviews["PROPERTY_VALUES"]["COMMENT"] =
                        $values["comment"];
                    $oElement = new CIBlockElement();
                    $oElement->Add($this->commentFieldsIBlockReviews);
                    echo "<meta http-equiv='refresh' content='0'>";
                } else {
                    ShowError(
                        strError: "Поля: 'Имя' и 'Комментарий', обязательны для заполнения!",
                    );
                }
            }
        }

        /**
         * ------------------------------------------------------------------------------------------#
         * Проверка данных формы ↓
         * ---------------------------------------------------------------------------------------#
         *
         * @param $data
         *
         * @return string
         */
        private function checkFormData(
            $data,
        ): string {
            return htmlspecialchars(stripslashes(trim($data)));
        }

        /**
         *------------------------------------------------------------------------------------------#
         * Получаем экземпляр класса \Bitrix\Main\HttpRequest ↓
         *---------------------------------------------------------------------------------------#
         *
         * @return \Bitrix\Main\HttpRequest
         */
        public function getCommentRequestObject(): HttpRequest
        {
            return Application::getInstance()->getContext()
                ->getRequest();
        }
    }
