<?php

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }

    use Bitrix\Main\{Application, HttpRequest, Loader, LoaderException};

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
         * Объект запроса добавления комментария.
         *
         * @var HttpRequest
         */
        private HttpRequest $commentRequest;

        /**
         * Объект результата выборки элементов инфоблока.
         *
         * @var CIBlockResult
         */
        private CIBlockResult $iBlockResult;

        /**
         * Данные информационного блока "отзывы".
         *
         * @var array|bool
         */
        private array|bool $iBlock;

        /**
         * Массив элементов инфоблока сформированный после выборки.
         *
         * @var array
         */
        private array $arElements = [];

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
        private array $arFieldsComment = [
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
        private array $ratingEnumFields = [];

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

            $this->iBlock = CIBlock::GetList(
                arOrder: [],
                arFilter: ["CODE" => "reviews"],
            )
                ->GetNext();

            $arParams["REVIEWS_COUNT"] = intval($arParams["REVIEWS_COUNT"]);

            if ($arParams["REVIEWS_COUNT"] <= 0) {
                $arParams["REVIEWS_COUNT"] = 6;
            }

            $this->iBlockResult = CIBlockElement::GetList(
                arOrder: [$arParams["SORT_BY"] => $arParams["SORT_ORDER"]],
                arFilter: [$this->iBlock["ID"]],
                arGroupBy: false,
                arNavStartParams: ["nPageSize" => $arParams["REVIEWS_COUNT"]],
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

            $this->commentRequest =
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
            if (!$this->checkIBlockReviews()) {
                ShowError(
                    strError: "Отсутствует информационный блок 'Отзывы'!
	                Для использования компонента необходимо создать информационный блок \"Отзывы\",
	                с символьным кодом: \"reviews\", содержащий свойства:
	                - \"Комментарий\" (тип - строка, код свойства - \"COMMENT\", обязательное к заполнению);
	                - \"Оценка\" (тип - список, код свойства - \"RATING\", значения списка от 1 до 5).",
                );
            }
            $this->getIBlockElements($this->iBlockResult);

            $this->arResult["IBLOCK"] = $this->iBlock;

            $this->arResult["ITEMS"] = $this->arElements;

            $this->arResult["RATING"] = $this->ratingEnumFields;

            $this->arResult["NAV_STRING"] = $this->navString;

            $this->addComment($this->commentRequest);

            $this->includeComponentTemplate();
        }

        /**
         * ------------------------------------------------------------------------------------------#
         * Метод проверки наличия модуля Инфоблоки, а также существования
         * инфоблока "Отзывы", с символьным кодом "reviews" ↓
         * ----------------------------------------------------------------------------------------#
         *
         * @return bool
         *
         * @throws LoaderException
         *
         */
        private function checkIBlockReviews(): bool
        {
            if (!Loader::IncludeModule("iblock") || !$this->iBlock) {
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
        private function getIBlockElements(
            CIBlockResult $iBlockResult,
        ): void {
            while ($element = $iBlockResult->GetNextElement()) {
                $id = $element->fields["ID"];
                $this->arElements[$id] = $element->GetFields();
            }

            $this->navString = $this->iBlockResult->GetPageNavStringEx(
                navComponentObject: $navComponentObject,
                navigationTitle: '',
                templateName: "modern",
            );

            $ratingEnums = CIBlockPropertyEnum::GetList(
                ["DEF" => "DESC", "SORT" => "DESC"],
                ["IBLOCK_ID" => $this->iBlock["ID"], "CODE" => "RATING"],
            );

            while ($enum_fields = $ratingEnums->GetNext()) {
                $value = $enum_fields["VALUE"];
                $this->ratingEnumFields[$value] = $enum_fields;
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
        private function addComment(
            HttpRequest $request,
        ): void {
            $values = $request->getPostList()->toArray();
            if (!empty($values)) {
                if (!empty($values["name"] && !empty($values["comment"]))) {
                    $values["name"] = $this->checkFormData($values['name']);
                    $values["comment"] =
                        $this->checkFormData($values['comment']);
                    $this->arFieldsComment["IBLOCK_ID"] = $this->iBlock["ID"];
                    $this->arFieldsComment["PREVIEW_TEXT"] = $values["name"];
                    $values["name"] = sprintf("%s_%s", $values["name"], rand());
                    $this->arFieldsComment["NAME"] = $values["name"];
                    $code = CUtil::translit(
                        $values['name'],
                        "ru",
                        $this->translitCodeParams,
                    );
                    $this->arFieldsComment["CODE"] = $code;
                    $this->arFieldsComment["PROPERTY_VALUES"]["RATING"] =
                        $values["rating"];
                    $this->arFieldsComment["PROPERTY_VALUES"]["COMMENT"] =
                        $values["comment"];
                    $oElement = new CIBlockElement();
                    $oElement->Add($this->arFieldsComment);
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
    }
