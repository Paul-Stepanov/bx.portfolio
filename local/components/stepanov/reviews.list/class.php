<?php
	if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
		die();
	}

	use Bitrix\Main\{Loader, Application, HttpRequest, LoaderException};

	/**------------------------------------------------------------------------------------------#
	 * Для использования компонента необходимо создать информационный блок "Отзывы",
	 * с символьным кодом: "reviews", содержащий свойства:
	 * - "Комментарий" (тип - строка, код свойства - "COMMENT", обязательное к заполнению);
	 * - "Оценка" (тип - список, код свойства - "RATING", значения списка от 1 до 5).
	 * Для хранения имени комментатора используется поле инфоблока "Анонс" ("PREVIEW_TEXT"),
	 * текста комментария - свойство "Комментарий" ("COMMENT"), оценки - свойство "Оценка" ("RATING").
	 * #------------------------------------------------------------------------------------------*/
	class ReviewsList extends CBitrixComponent {
		/**
		 * @var array
		 * Массив значений для постраничной навигации.
		 */
		private static array $arNavStartParams = [];
		/**
		 * @var array
		 * Массив названий шаблонов для постраничной навигации.
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
		 * @var string|false
		 * Постраничная навигация.
		 */
		private static string|false $navString;
		/**
		 * @var HttpRequest
		 * Объект запроса добавления комментария.
		 */
		private static HttpRequest $commentRequest;
		/**
		 * @var CIBlockResult
		 * Объект результата выборки элементов инфоблока
		 */
		private static CIBlockResult $iBlockResult;
		/**
		 * @var array|string[]
		 * Массив со значениями полей для фильтрации инфоблока.
		 */
		private static array $arFilterIBlock = [
			"CODE" => "reviews",
		];
		/**
		 * @var array|bool
		 * Данные информационного блока "отзывы".
		 */
		private static array|bool $iBlock;
		/**
		 * @var array
		 * Массив со значениями полей для сортировки элементов инфоблока.
		 */
		private static array $arOrderElements = [];
		/**
		 * @var array
		 * Массив со значениями полей для фильтрации элементов инфоблока.
		 */
		private static array $arFilterIBlockElements = [];
		/**
		 * @var array|string[]
		 * Массив возвращаемых полей элемента инфоблока.
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
		 * @var array
		 * Массив элементов инфоблока сформированный после выборки.
		 */
		private static array $arElements = [];
		/**
		 * @var array|string[]
		 * Массив значений для осуществления транслитерации символьного кода элемента.
		 */
		private static array $translitCodeParams = [
			"max_len" => "100", // обрезает символьный код до 100 символов
			"change_case" => "L", // буквы преобразуются к нижнему регистру
			"replace_space" => "_", // меняем пробелы на нижнее подчеркивание
			"replace_other" => "_", // меняем левые символы на нижнее подчеркивание
			"delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
		];
		/**
		 * @var array
		 * Массив полей заносимых в информационный блок при создании элемента комментария.
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
		 * @var array
		 * Массив полей рейтинга типа значение->ID.
		 */
		private static array $ratingEnumFields = [];

		/**
		 * @param $arParams
		 *
		 * @return array
		 * /*------------------------------------------------------------------------------------------#
		 * Подготовка параметров компонента ↓
		 * #------------------------------------------------------------------------------------------
		 * @throws LoaderException
		 */
		public function onPrepareComponentParams($arParams): array {
			Loader::IncludeModule("iblock");
			self::$iBlock = CIBlock::GetList(
				arOrder:  [],
				arFilter: self::$arFilterIBlock)
				->GetNext();
			self::$arOrderElements = [
				$arParams["SORT_BY"] => $arParams["SORT_ORDER"],
			];
			$arParams["REVIEWS_COUNT"] = intval($arParams["REVIEWS_COUNT"]);
			if($arParams["REVIEWS_COUNT"] <= 0) {
				$arParams["REVIEWS_COUNT"] = 6;
			}
			self::$arNavStartParams["nPageSize"] = $arParams["REVIEWS_COUNT"];
			self::$arFilterIBlockElements["IBLOCK_ID"] = self::$iBlock["ID"];
			self::$iBlockResult = CIBlockElement::GetList(
				arOrder:          self::$arOrderElements,
				arFilter:         self::$arFilterIBlockElements,
				arGroupBy:        false,
				arNavStartParams: self::$arNavStartParams,
				arSelectFields:   self::$arSelectElements);
			self::$commentRequest = Application::getInstance()->getContext()->getRequest();

			return $arParams;
		}

		/**
		 * @return void
		 * @throws LoaderException
		 * @throws Exception
		 * /*------------------------------------------------------------------------------------------#
		 * Формирование массива arResult и передача его в шаблон компонента ↓
		 * #------------------------------------------------------------------------------------------*/
		public
		function executeComponent(): void {
			if( !self::checkIBlockReviews()) {
				ShowError(strError: "Отсутствует информационный блок 'Отзывы'!
	  Для использования компонента необходимо создать информационный блок \"Отзывы\",
	  с символьным кодом: \"reviews\", содержащий свойства:
	  - \"Комментарий\" (тип - строка, код свойства - \"COMMENT\", обязательное к заполнению);
	  - \"Оценка\" (тип - список, код свойства - \"RATING\", значения списка от 1 до 5).");
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
		 * @return bool
		 * @throws LoaderException
		 * /*------------------------------------------------------------------------------------------#
		 * Метод проверки наличия модуля Инфоблоки, а также существования инфоблока "Отзывы",
		 * с символьным кодом "reviews" ↓
		 * #------------------------------------------------------------------------------------------*/
		private
		static function checkIBlockReviews(): bool {
			if( !Loader::IncludeModule("iblock") || !self::$iBlock) {
				return false;
			} else {
				return true;
			}
		}

		/**
		 * @param  CIBlockResult  $iBlockResult
		 *
		 * @return void
		 * /*------------------------------------------------------------------------------------------#
		 * Выбираем элементы инфоблока "Отзывы" с необходимыми полями ↓
		 * #------------------------------------------------------------------------------------------
		 */
		private
		static function getIBlockElements(
			CIBlockResult $iBlockResult): void {
			while($element = $iBlockResult->GetNextElement()) {
				$id = $element->fields["ID"];
				self::$arElements[$id] = $element->GetFields();
			}
			self::$navString = self::$iBlockResult->GetPageNavStringEx(
				navComponentObject: $navComponentObject,
				navigationTitle:    '',
				templateName:       self::$arTemplatePageNavName["modern"]);
			$ratingEnums = CIBlockPropertyEnum::GetList(["DEF" => "DESC", "SORT" => "DESC"],
			                                            ["IBLOCK_ID" => self::$iBlock["ID"], "CODE" => "RATING"]);
			while($enum_fields = $ratingEnums->GetNext()) {
				$value = $enum_fields["VALUE"];
				self::$ratingEnumFields[$value] = $enum_fields;
			}
		}

		/**
		 * @param  HttpRequest  $request
		 *
		 * @return void
		 * /*------------------------------------------------------------------------------------------#
		 * Добавляем комментарий в инфоблок "Отзывы" ↓
		 * #------------------------------------------------------------------------------------------
		 * @throws Exception
		 */
		private
		static function addComment(
			HttpRequest $request): void {
			$values = $request->getPostList()->toArray();
			if( !empty($values)) {
				if( !empty($values["name"] && !empty($values["comment"]))) {
					$values["name"] = self::checkFormData($values['name']);
					$values["comment"] = self::checkFormData($values['comment']);
					self::$arFieldsComment["IBLOCK_ID"] = self::$iBlock["ID"];
					self::$arFieldsComment["PREVIEW_TEXT"] = $values["name"];
					$values["name"] = sprintf("%s_%s", $values["name"], rand());
					self::$arFieldsComment["NAME"] = $values["name"];
					$code = CUtil::translit($values['name'], "ru", self::$translitCodeParams);
					self::$arFieldsComment["CODE"] = $code;
					self::$arFieldsComment["PROPERTY_VALUES"]["RATING"] = $values["rating"];
					self::$arFieldsComment["PROPERTY_VALUES"]["COMMENT"] = $values["comment"];
					$oElement = new CIBlockElement();
					$oElement->Add(self::$arFieldsComment);
					echo "<meta http-equiv='refresh' content='0'>";
				} else {
					ShowError(strError: "Поля: 'Имя' и 'Комментарий', обязательны для заполнения!");
				}
			}
		}

		/**
		 * @param $data
		 *
		 * @return string
		 * /*------------------------------------------------------------------------------------------#
		 * Проверка данных формы ↓
		 * #------------------------------------------------------------------------------------------*/
		private
		static function checkFormData(
			$data): string {
			$data = trim($data);
			$data = stripslashes($data);

			return htmlspecialchars($data);
		}
	}
