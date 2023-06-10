<?php

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }

    use Bitrix\Main\{Application, HttpRequest, Loader, LoaderException};

    /**------------------------------------------------------------------------------------------#
     * Для использования компонента необходимо создать информационный блок "Отзывы",
     * с символьным кодом: "reviews", содержащий свойства:
     * - "Комментарий" (тип - строка, код свойства - "COMMENT", обязательное к заполнению);
     * - "Оценка" (тип - список, код свойства - "RATING", значения списка от 1 до 5).
     * Для хранения имени комментатора используется поле инфоблока "Анонс" ("PREVIEW_TEXT"),
     * текста комментария - свойство "Комментарий" ("COMMENT"), оценки - свойство "Оценка" ("RATING").
     * #------------------------------------------------------------------------------------------*/
    class ReviewsList extends CBitrixComponent
    {
        /**
         * Массив значений для постраничной навигации.
         *
         * @var array
         */
        private static array $arNavStartParams = [];
        /**
         * Массив названий шаблонов для постраничной навигации.
         *
         * @var array
         */
        private static array $arTemplatePageNavName = [
            ".default" => ".default",
            "arrows" => "arrows",
            "arrows_adm" => "arrows_adm",
            "bootstrap_v4" => "bootstrap_v4",
            "grid" => "grid",
            "js" => "js",
            "modern" => "modern",
            "orange" => "orange",
            "round" => "round",
            "visual" => "visual",
        ];
        /**
         * Постраничная навигация.
         *
         * @var string|false
         */
        private static string|false $navString;
        /**
         * Объект запроса добавления комментария.
         *
         * @var HttpRequest
         */
        private static HttpRequest $commentRequest;
        /**
         * Объект результата выборки элементов инфоблока.
         *
         * @var CIBlockResult
         */
        private static CIBlockResult $iBlockResult;
        /**
         * Массив со значениями полей для фильтрации инфоблока.
         *
         * @var array|string[]
         */
        private static array $arFilterIBlock = [
            "CODE" => "reviews",
        ];
        /**
         * Данные информационного блока "отзывы".
         *
         * @var array|bool
         */
        private static array|bool $iBlock;
        /**
         * Массив со значениями полей для сортировки элементов инфоблока.
         *
         * @var array
         */
        private static array $arOrderElements = [];
        /**
         * Массив со значениями полей для фильтрации элементов инфоблока.
         *
         * @var array
         */
        private static array $arFilterIBlockElements = [];
        /**
         * Массив возвращаемых полей элемента инфоблока.
         *
         * @var array|string[]
         */
        private static array $arSelectElements = [
            "ID",
            "IBLOCK_ID",
            "IBLOCK_NAME",
            "NAME",
            "PREVIEW_TEXT",
            "DATE_ACTIVE_FROM",
            "PROPERTY_COMMENT",
            "PROPERTY_RATING",
        ];
        /**
         * Массив элементов инфоблока сформированный после выборки.
         *
         * @var array
         */
        private static array $arElements = [];
        /**
         * Массив значений для осуществления транслитерации символьного кода элемента.
         *
         * @var array|string[]
         */
        private static array $translitCodeParams = [
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
         * Массив полей заносимых в информационный блок при создании элемента комментария.
         *
         * @var array
         */
        private static array $arFieldsComment = [
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
         * Массив полей рейтинга типа значение->ID.
         *
         * @var array
         */
        private static array $ratingEnumFields = [];

        /**
         *------------------------------------------------------------------------------------------#
         * Подготовка параметров компонента ↓
         * ---------------------------------------------------------------------------------------#
         *
         * @param $arParams
         *
         * @return array
         *
         * @throws LoaderException
         *
         */
        public function onPrepareComponentParams($arParams): array
        {
            Loader::IncludeModule("iblock");
            self::$iBlock = CIBlock::GetList(
                arOrder: [],
                arFilter: self::$arFilterIBlock,
            )
                ->GetNext();
            self::$arOrderElements = [
                $arParams["SORT_BY"] => $arParams["SORT_ORDER"],
            ];
            $arParams["REVIEWS_COUNT"] = intval($arParams["REVIEWS_COUNT"]);
            if ($arParams["REVIEWS_COUNT"] <= 0) {
                $arParams["REVIEWS_COUNT"] = 6;
            }
            self::$arNavStartParams["nPageSize"] = $arParams["REVIEWS_COUNT"];
            self::$arFilterIBlockElements["IBLOCK_ID"] = self::$iBlock["ID"];
            self::$iBlockResult = CIBlockElement::GetList(
                arOrder: self::$arOrderElements,
                arFilter: self::$arFilterIBlockElements,
                arGroupBy: false,
                arNavStartParams: self::$arNavStartParams,
                arSelectFields: self::$arSelectElements,
            );
            self::$commentRequest =
                Application::getInstance()->getContext()->getRequest();

            return $arParams;
        }

        /**
         *------------------------------------------------------------------------------------------#
         * Формирование массива arResult и передача его в шаблон компонента ↓
         * ---------------------------------------------------------------------------------------#
         *
         * @return void
         *
         * @throws LoaderException
         *
         * @throws Exception
         *
         */
        public function executeComponent(): void
        {
            if (!self::checkIBlockReviews()) {
                ShowError(
                    strError: "Отсутствует информационный блок 'Отзывы'!
	                Для использования компонента необходимо создать информационный блок \"Отзывы\",
	                с символьным кодом: \"reviews\", содержащий свойства:
	                - \"Комментарий\" (тип - строка, код свойства - \"COMMENT\", обязательное к заполнению);
	                - \"Оценка\" (тип - список, код свойства - \"RATING\", значения списка от 1 до 5).",
                );
            }
            self::getIBlockElements(self::$iBlockResult);
            $this->arResult["IBLOCK"] = self::$iBlock;
            $this->arResult["ITEMS"] = self::$arElements;
            $this->arResult["RATING"] = self::$ratingEnumFields;
            $this->arResult["NAV_STRING"] = self::$navString;
            self::addComment(self::$commentRequest);
            $this->includeComponentTemplate();
        }

        /**
         * ------------------------------------------------------------------------------------------#
         * Метод проверки наличия модуля Инфоблоки, а также существования инфоблока "Отзывы",
         * с символьным кодом "reviews" ↓
         * ----------------------------------------------------------------------------------------#
         *
         * @return bool
         *
         * @throws LoaderException
         *
         */
        private static function checkIBlockReviews(): bool
        {
            if (!Loader::IncludeModule("iblock") || !self::$iBlock) {
                return false;
            } else {
                return true;
            }
        }

        /**
         *------------------------------------------------------------------------------------------#
         * Выбираем элементы инфоблока "Отзывы" с необходимыми полями ↓
         * ---------------------------------------------------------------------------------------#
         *
         * @param  CIBlockResult  $iBlockResult
         *
         * @return void
         */
        private static function getIBlockElements(
            CIBlockResult $iBlockResult,
        ): void {
            while ($element = $iBlockResult->GetNextElement()) {
                $id = $element->fields["ID"];
                self::$arElements[$id] = $element->GetFields();
            }
            self::$navString = self::$iBlockResult->GetPageNavStringEx(
                navComponentObject: $navComponentObject,
                navigationTitle: '',
                templateName: self::$arTemplatePageNavName["modern"],
            );
            $ratingEnums = CIBlockPropertyEnum::GetList(
                ["DEF" => "DESC", "SORT" => "DESC"],
                ["IBLOCK_ID" => self::$iBlock["ID"], "CODE" => "RATING"],
            );
            while ($enum_fields = $ratingEnums->GetNext()) {
                $value = $enum_fields["VALUE"];
                self::$ratingEnumFields[$value] = $enum_fields;
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
         *
         * @throws Exception
         */
        private static function addComment(
            HttpRequest $request,
        ): void {
            $values = $request->getPostList()->toArray();
            if (!empty($values)) {
                if (!empty($values["name"] && !empty($values["comment"]))) {
                    $values["name"] = self::checkFormData($values['name']);
                    $values["comment"] =
                        self::checkFormData($values['comment']);
                    self::$arFieldsComment["IBLOCK_ID"] = self::$iBlock["ID"];
                    self::$arFieldsComment["PREVIEW_TEXT"] = $values["name"];
                    $values["name"] = sprintf("%s_%s", $values["name"], rand());
                    self::$arFieldsComment["NAME"] = $values["name"];
                    $code = CUtil::translit(
                        $values['name'],
                        "ru",
                        self::$translitCodeParams,
                    );
                    self::$arFieldsComment["CODE"] = $code;
                    self::$arFieldsComment["PROPERTY_VALUES"]["RATING"] =
                        $values["rating"];
                    self::$arFieldsComment["PROPERTY_VALUES"]["COMMENT"] =
                        $values["comment"];
                    $oElement = new CIBlockElement();
                    $oElement->Add(self::$arFieldsComment);
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
        private static function checkFormData(
            $data,
        ): string {
            $data = trim($data);
            $data = stripslashes($data);

            return htmlspecialchars($data);
        }
    }
