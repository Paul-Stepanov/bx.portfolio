<?php

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }
    $arSorts = ["ASC" => "По возрастанию", "DESC" => "По убыванию"];
    $arSortFields = [
        "NAME" => "Имя комментатора",
        "PROPERTY_COMMENT" => "Комментарий",
        "PROPERTY_RATING" => "Оценка",
    ];
    $arComponentParameters = [
        "GROUPS" => [],
        "PARAMETERS" => [
            "SORT_BY" => [
                "PARENT" => "DATA_SOURCE",
                "NAME" => "Поле для сортировки отзывов",
                "TYPE" => "LIST",
                "DEFAULT" => "RATING",
                "VALUES" => $arSortFields,
                "ADDITIONAL_VALUES" => "N",
            ],
            "SORT_ORDER" => [
                "PARENT" => "DATA_SOURCE",
                "NAME" => "Направление сортировки",
                "TYPE" => "LIST",
                "DEFAULT" => "DESC",
                "VALUES" => $arSorts,
                "ADDITIONAL_VALUES" => "N",
            ],
            "REVIEWS_COUNT" => [
                "PARENT" => "BASE",
                "NAME" => "Количество отзывов на странице",
                "TYPE" => "STRING",
                "DEFAULT" => "6",
            ],
        ],
    ];