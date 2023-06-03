<?php
	if( !defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
		die();
	}

	use Bitrix\Main\Page\Asset;

	global $APPLICATION;
?>

<!doctype html>
<html lang="ru">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width,initial-scale=1">

   <!-- Favicon ↓ -->
   <link rel="icon" href="<?= SITE_TEMPLATE_PATH ?>/assets/favicon-16x16.png" type="image/png">
   <link rel="icon" href="<?= SITE_TEMPLATE_PATH ?>/assets/favicon-32x32.png" type="image/png">
   <!-- Favicon ↑ -->

   <title><?php $APPLICATION->ShowTitle(); ?></title>
	<?php $APPLICATION->ShowHead(); ?>

   <!-- JS ↓ -->
   <script defer='defer' src="<?= SITE_TEMPLATE_PATH ?>/js/main.0728401aed93f67dc8e1.js"></script>
   <script defer='defer' src="<?= SITE_TEMPLATE_PATH ?>/js/jquery-3.7.0.min.js"></script>
   <script defer='defer' src="<?= SITE_TEMPLATE_PATH ?>/js/adminPanel.js"></script>
   <!-- JS ↑ -->

   <!-- CSS ↓ -->
	<?php Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/main.dd4c75cc1175bfe23934.css") ?>
	<?php Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/adminPanel.css") ?>
   <!-- CSS ↑ -->

</head>
<body>

<!-- Admin panel ↓ -->
<span class="addPanel">Адм.панель ↓</span>
<div class="adminPanel" style="display: none"> <?php $APPLICATION->ShowPanel(); ?> </div>
<!-- Admin panel ↑ -->

<!-- Header ↓ -->
<header class="header" id="header">

   <!-- Header top ↓ -->
   <div class="header__top">
      <div class="container">
         <nav class="header__menu">
            <div class="header__menu-bg"></div>
            <ul class="header__menu-list">
               <li class="header__list-item"><a href="#header" class="header__list-link">Главная</a></li>
               <li class="header__list-item"><a href="#about" class="header__list-link">Про меня</a></li>
               <li class="header__list-item"><a href="#work" class="header__list-link">Работы</a></li>
               <li class="header__list-item"><a href="#contacts" class="header__list-link">Контакты</a></li>
            </ul>
         </nav>
      </div>
   </div>
   <!-- Header top ↑ -->

   <!-- Header main ↓ -->
   <div class="header__main">
      <div class="container">
         <div class="header__main-wrapper">
            <div class="header__photo"><img class="header__image"
                                            src="<?= SITE_TEMPLATE_PATH ?>/assets/photo_main_cut-photo.ru.jpg"
                                            alt="photo">
            </div>
            <div class="header__content">
               <div class="header__content-inner"><h1 class="header__name">Pavel Stepanov</h1>
                  <h2 class="header__profession">Web developer</h2>
                  <div class="header__contacts">
                     <div class="header__contacts-title">Телефон:<p class="header__contacts-subtitle">
                           8-950-917-46-60</p></div>
                     <div class="header__contacts-title">Email:
                        <div class="header__contacts-subtitle"><a href="mailto:p.stepanov13@mail.ru">p.stepanov13@mail.ru</a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Header main ↑ -->

</header>
<!-- Header ↑ -->
