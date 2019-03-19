/**
 * Created by Rain on 19.03.2019.
 */
/*
 * --------------------------------------------------------------------------------------------------------------------
 *                                      Public Variables и загрузка страницы
 * --------------------------------------------------------------------------------------------------------------------
 */
var subdivision = $('div#subdivision').data('subdivision'); //Текущее подразделение
var parser_content = $('#parser-content');
/*
 * Загрузка страницы
 * Каждый раз при перезагрузке страницы, браузер будет подгружать через AJAX именно ту часть которая была подгружена
 * до перезагрузки. Достигается это засчет сканирования состояния которое я устанвливаю как ключ - значение в
 * localStorage браузера.
 */
$(function () {
    var state = localStorage.getItem("parserState");
    switch (state){
        case "main": showMainData(); break;
        case "tanks": showTanksData(); break;
        case "outcomes": showOutcomesData(); break;
        case "incomes": showIncomesData(); break;
        case "office": showOfficeData(); break;
        default: showMainData(); break; //Если состояние еще не установлено, будет подгружаться заданная страница
}
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
    //Устанавливаю заголовок
    $("#title").text("Емкости");
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
        //Выделяю кнопку
        $("#parser-main").addClass('active');
        //Подгружаю контент
        $("#parser-content").html(response);
        //Устанавливаю заголовок
        $("#title").text("Parser");
    });
}
/**
 * Подгружает AJAX контент с данными о топливе в емкостях
 */
function showTanksData() {
    var subdivision = $("#subdivisions").val();
    var request = $.ajax({
        type: "POST",
        url: "/parser/tanks/",
        data:{"subdivision": subdivision},
        cache: false
    });
    request.done(function (response) {
        //Выделяю кнопку
        $("#parser-tanks").addClass('active');
        //Подгружаю контент
        $("#parser-content").html(response);

    });
}