<?php

    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
    $APPLICATION->SetTitle("");
    global $APPLICATION;
    $APPLICATION->SetPageProperty('title', 'Stepanov Pavel');
?><?
    $APPLICATION->IncludeComponent(
        "stepanov:reviews.list",
        "",
        [
            "REVIEWS_COUNT" => "6",
            "SORT_BY" => "PROPERTY_RATING",
            "SORT_ORDER" => "DESC",
        ],
    ); ?> <!-- Main ↓ -->
    <main class="main">
        <!-- About ↓ -->
        <section class="about" id="about">
            <div class="container">
                <div class="about__wrapper">
                    <div class="about__left-content">
                        <div class="about__education">
                            <div class="about__education-title title">
                                Образование:
                            </div>
                            <div class="about__education-list">
                                <p class="about__education-list-title">
                                    Донской техникум информатики и
                                    вычислительной техники
                                </p>
                                <p class="about__education-list-date">
                                    2007-2011
                                </p>
                                <p class="about__education-list-specialization">
                                    Специальность: "Вычислительные системы,
                                    комплексы и сети"
                                </p>
                            </div>
                            <div class="about__education-list">
                                <p class="about__education-list-title">
                                    Тульский институт экономики и информатики
                                </p>
                                <p class="about__education-list-date">
                                    2012-2016
                                </p>
                                <p class="about__education-list-specialization">
                                    Специальность: "Юриспруденция"
                                </p>
                            </div>
                        </div>
                        <div class="about__hobbies">
                            <div class="about__hobbies-title title">
                                Хобби:
                            </div>
                            <div class="about__hobbies-list">
                                <img src="/local/templates/my_portfolio/assets/icons8-sport-64.png"
                                     class="about__hobbies-list-img" alt="">
                                <p class="about__hobbies-list-text">
                                    Спорт
                                </p>
                            </div>
                            <div class="about__hobbies-list">
                                <img src="/local/templates/my_portfolio/assets/icons8-listening-to-music-on-headphones-64.png"
                                     class="about__hobbies-list-img" alt="">
                                <p class="about__hobbies-list-text">
                                    Музыка
                                </p>
                            </div>
                            <div class="about__hobbies-list">
                                <img src="/local/templates/my_portfolio/assets/icons8-film-reel-64.png"
                                     class="about__hobbies-list-img" alt="">
                                <p class="about__hobbies-list-text">
                                    Фильмы
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="about__right-content">
                        <div class="about__title title">
                            Про меня:
                        </div>
                        <div class="about__description">
                            Привет, меня зовут Павел и я начинающий
                            веб-разработчик! Программированием я начал
                            заниматься в 2021 году, благодяря моей предыдущей
                            работе. В прошлом, за 10 лет я прошел путь от
                            рядового сотрудника до заместителя начальника отдела
                            регионального управления, где для автоматизации
                            рутинных процесов я и наткнулся на веб-разработку,
                            погрузившись в которую понял, что всю свою
                            дальнейшую жизнь я хочу посвятить этому прекрасному
                            занятию.
                        </div>
                        <div class="about__stack title">
                            Мой стэк технологий:
                        </div>
                        <div class="about__stack-body">
                            <div class="about__stack-item">
                                <img src="/local/templates/my_portfolio/assets/icons8-html-5-is-a-software-solution-stack-that-defines-the-properties-and-behaviors-of-web-page-80.png"
                                     class="about__stack-img" alt="">
                                <div class="about__stack-title">
                                    HTML5
                                </div>
                            </div>
                            <div class="about__stack-item">
                                <img src="/local/templates/my_portfolio/assets/icons8-css3-80.png"
                                     class="about__stack-img" alt="">
                                <div class="about__stack-title">
                                    CSS3
                                </div>
                            </div>
                            <div class="about__stack-item">
                                <img src="/local/templates/my_portfolio/assets/icons8-sass-80.png"
                                     class="about__stack-img" alt="">
                                <div class="about__stack-title">
                                    SCSS
                                </div>
                            </div>
                            <div class="about__stack-item">
                                <img src="/local/templates/my_portfolio/assets/icons8-javascript-80.png"
                                     class="about__stack-img" alt="">
                                <div class="about__stack-title">
                                    Java Script
                                </div>
                            </div>
                            <div class="about__stack-item">
                                <img src="/local/templates/my_portfolio/assets/icons8-jquery-is-a-javascript-library-designed-to-simplify-html-80.png"
                                     class="about__stack-img" alt="">
                                <div class="about__stack-title">
                                    JQuery
                                </div>
                            </div>
                            <div class="about__stack-item">
                                <img src="/local/templates/my_portfolio/assets/icons8-php-logo-80.png"
                                     class="about__stack-img" alt="">
                                <div class="about__stack-title">
                                    PHP
                                </div>
                            </div>
                            <div class="about__stack-item">
                                <img src="/local/templates/my_portfolio/assets/icons8-laravel-80.png"
                                     class="about__stack-img" alt="">
                                <div class="about__stack-title">
                                    Laravel
                                </div>
                            </div>
                            <div class="about__stack-item">
                                <img src="/local/templates/my_portfolio/assets/icons8-mysql-logo-80.png"
                                     class="about__stack-img" alt="">
                                <div class="about__stack-title">
                                    MySQL
                                </div>
                            </div>
                            <div class="about__stack-item">
                                <img src="/local/templates/my_portfolio/assets/icons8-webpack-80.png"
                                     class="about__stack-img" alt="">
                                <div class="about__stack-title">
                                    Webpack
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- About ↑ --> <!-- Work ↓ -->
        <section class="work" id="work">
            <div class="container">
                <div class="work__wrapper">
                    <div class="work__title title">
                        Мои работы:
                    </div>
                    <div class="work__body">
                        <div class="work__body-inner">
                            <img src="/local/templates/my_portfolio/assets/1.png"
                                 class="work__image work__image--active"> <img
                                    src="/local/templates/my_portfolio/assets/2.png"
                                    class="work__image work__image--next"> <img
                                    src="/local/templates/my_portfolio/assets/3.png"
                                    class="work__image work__image--prev">
                            <div class="work__image-bg">
                                <a href="https://github.com/Paul-Stepanov/Detentions"
                                   target="_blank" class="work__bg-link"><img
                                            src="/local/templates/my_portfolio/assets/icons8-github-64.png"
                                            alt="" class="work__link-img"></a>
                            </div>
                            <div class="work__image-arrow-body work__image-arrow-body--left">
                                <div class="work__image-arrow work__image-arrow--left">
                                </div>
                            </div>
                            <div class="work__image-arrow-body work__image-arrow-body--right">
                                <div class="work__image-arrow work__image-arrow--right">
                                </div>
                            </div>
                        </div>
                        <div class="work__body-inner">
                            <img src="/local/templates/my_portfolio/assets/4.png"
                                 class="work__image work__image--active"> <img
                                    src="/local/templates/my_portfolio/assets/5.png"
                                    class="work__image work__image--next"> <img
                                    src="/local/templates/my_portfolio/assets/6.png"
                                    class="work__image work__image--prev">
                            <div class="work__image-bg">
                                <a href="https://github.com/Paul-Stepanov/first_php_site"
                                   target="_blank" class="work__bg-link"><img
                                            src="/local/templates/my_portfolio/assets/icons8-github-64.png"
                                            alt="" class="work__link-img"></a>
                            </div>
                            <div class="work__image-arrow-body work__image-arrow-body--left">
                                <div class="work__image-arrow work__image-arrow--left">
                                </div>
                            </div>
                            <div class="work__image-arrow-body work__image-arrow-body--right">
                                <div class="work__image-arrow work__image-arrow--right">
                                </div>
                            </div>
                        </div>
                        <div class="work__body-inner">
                            <img src="/local/templates/my_portfolio/assets/07.PNG"
                                 class="work__image work__image--active"> <img
                                    src="/local/templates/my_portfolio/assets/08.PNG"
                                    class="work__image work__image--next"> <img
                                    src="/local/templates/my_portfolio/assets/09.PNG"
                                    class="work__image work__image--prev">
                            <div class="work__image-bg">
                                <a href="https://github.com/Paul-Stepanov/training_landing"
                                   target="_blank" class="work__bg-link"><img
                                            src="/local/templates/my_portfolio/assets/icons8-github-64.png"
                                            alt="" class="work__link-img"></a>
                            </div>
                            <div class="work__image-arrow-body work__image-arrow-body--left">
                                <div class="work__image-arrow work__image-arrow--left">
                                </div>
                            </div>
                            <div class="work__image-arrow-body work__image-arrow-body--right">
                                <div class="work__image-arrow work__image-arrow--right">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
        </section>
        <!-- Work ↑ -->
    </main>
    <!-- Main ↑ --><?php
    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>