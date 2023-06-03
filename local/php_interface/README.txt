По умолчанию система ожидает увидеть файл composer.json в папке bitrix,
однако если необходимо разместить его где-либо в другом месте проекта,
нужно указать путь до файла в .settings.php, чтобы его конфигурация
могла быть использована в продукте.

!!!!!!!!!!!!!!!!!!!!!!!!---> Файл .settings.php: <---!!!!!!!!!!!!!!!!!!!!!!!!

<?php
return [
  'composer' => [
    'value' => ['config_path' => '/path/to/your/composer.json']
  ],
  // ...
];
В нем необходимо подключить файл с зависимостями bitrix/composer-bx.json
с помощью плагина Composer Merge Plugin. В минимальном виде composer.json должен содержать
вызов плагина и подключение конфигурации Разработчиков Bitrix Framework.

Файл composer.json (можно скопировать из bitrix/composer.json.example).