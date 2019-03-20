/**
 * Created by Rain on 19.03.2019.
 */
/*
 * --------------------------------------------------------------------------------------------------------------------
 *                                      Public Variables и загрузка страницы
 * --------------------------------------------------------------------------------------------------------------------
 */
/*
 * Загрузка страницы
 * Каждый раз при перезагрузке страницы, браузер будет подгружать через AJAX именно ту часть которая была подгружена
 * до перезагрузки. Достигается это засчет сканирования состояния которое я устанвливаю как ключ - значение в
 * localStorage браузера.
 */
$(function () {
    var state = localStorage.getItem("parserState");
    switch (state){
        case "main": showMainData(); $("#parser-main").addClass('active'); break;
        case "tanks": showTanksPage(); $("#parser-tanks").addClass('active'); break;
        case "outcomes": showOutcomesData(); break;
        case "incomes": showIncomesData(); break;
        case "office": showOfficeData(); break;
        default: showMainData(); break; //Если состояние еще не установлено, будет подгружаться заданная страница
    }
    $(".dropdown-toggle").dropdown();
});
/*
 * --------------------------------------------------------------------------------------------------------------------
 *                                          Функции обработчики событий
 * --------------------------------------------------------------------------------------------------------------------
 */

/**
 * Обрабатывает нажатие на вкладку "Главная".
 */
$("#parser-main").on("click", function () {
    //Установка состояния
    localStorage.setItem("parserState", "main");
    //Выполнение AJAX запроса, загрузка контента
    showMainData();
});
/**
 * Обрабатывает нажатие на вкладку "Топливо в емкостях".
 */
$("#parser-tanks").on("click", function () {
    //Установка состояния
    localStorage.setItem("parserState", "tanks");
    //Выполнение AJAX запроса, загрузка контента
    showTanksPage();
});
/**
 * Обрабатывает нажатие на вкладку "Отпуск топлива".
 */
$("#parser-outcomes").on("click", function () {
    //Установка состояния
    localStorage.setItem("parserState", "outcomes");
    //Выполнение AJAX запроса, загрузка контента
    showOutcomesData();
});
/**
 * Обрабатывает нажатие на вкладку "Принятое топливо".
 */
$("#parser-incomes").on("click", function () {
    //Установка состояния
    localStorage.setItem("parserState", "incomes");
    //Выполнение AJAX запроса, загрузка контента
    showIncomesData();
});
/**
 * Обрабатывает нажатие на вкладку "Карты Top Don".
 */
$("#parser-office").on("click", function () {
    //Установка состояния
    localStorage.setItem("parserState", "office");
    //Выполнение AJAX запроса, загрузка контента
    showOfficeData();
});
/*
 * --------------------------------------------------------------------------------------------------------------------
 *                                                  Функции AJAX
 * --------------------------------------------------------------------------------------------------------------------
 */

/**
 * Подгружает AJAX контент с по главной странице
 */
function showMainData() {
    var request = $.ajax({
        type: "POST",
        url: "/parser/main/",
        cache: false
    });
    request.done(function (response) {
        //Подгружаю контент
        $("#parser-content").html(response);
        //Устанавливаю заголовок
        $("#title").text("Parser");
    });
}
/**
 * Подгружает панель навигации
 */
function showTanksPage() {
    //Очистка раздела
    $("div#parser-content").empty();
    //Формирую строку с панелью навигации
    var string = '<div class="parser-nav-bar">' +
                    '<div class="parser-nav-bar-container">' +
                        '<a href="" onclick="showTanksData();return false;" class="btn btn-success btn-sm">' +
                        '<i class="fa fa-upload" aria-hidden="true"></i> Загрузить XML</a>' +
                    '</div>' +
                    '<div class="parser-nav-bar-container">' +
                        '<a href="" onclick="cleanTanksPage();return false;" class="btn btn-danger btn-sm">' +
                        '<i class="fa fa-broom" aria-hidden="true"></i> Очистить</a>' +
                    '</div>' +
                 '</div>' +
                 '<div id="tanks-content"></div>';
    //прикрепляю к разделу панель навигации
    $("#parser-content").prepend(string);
    //Устанавливаю заголовок страницы
    $("#title").text("Емкости");
}
/**
 * Очистка раздела с контентом емкостей
 */
function cleanTanksPage() {
    $("div#tanks-content").empty();
}
/**
 * Подгружает AJAX контент с данными о топливе в емкостях
 */
function showTanksData() {
    var subdivision = $("#subdivisions").val();
    var request = $.ajax({
        type: "POST",
        url: "/parser/tanks/",
        data:{"subdivision": 4}, //Временно указал явное значение подразделения
        cache: false
    });
    request.done(function (response) {
        //Подгружаю контент
        $("#tanks-content").html(response);
    });
}