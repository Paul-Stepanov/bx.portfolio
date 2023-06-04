<?php

	use Bitrix\Main\EventManager;

	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/autoload.php")) {
		require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/autoload.php");
	}
	/**-----------------------------------------------------------------------------------#
	 * Создаем инстанс обработчика событий ↓
	 * #--------------------------------------------------------------------------------*/
	$eventManager = EventManager::getInstance();
	/**--------------------------------------------------------------------------------------#
	 * Регистрируем обработчик событий на запрет обновления свойств
	 * элементов инфоблока "Отзывы" ↓
	 * #-----------------------------------------------------------------------------------*/
	$eventManager->addEventHandler('iblock', 'OnBeforeIBlockElementUpdate',
	                               ['Stepanov\App\MyIBlockHandler', 'notUpdateCommentAndRating']);

