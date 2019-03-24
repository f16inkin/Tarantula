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
    var request = $.ajax({
        type: "POST",
        url: "/parser/tanks/",
        cache: false
    });
    request.done(function (response) {
        //Подгружаю контент
        $("#parser-content").html(response);
    });
}
/**
 * Очистка раздела с контентом емкостей
 */
function cleanTanksPage() {
    $("div#tanks-content").empty();
    $('#hidden-button').attr('hidden', true);
}
/**
 * Подгружает AJAX контент с данными о топливе в емкостях
 */
function showTanksData() {
    var subdivision = $("#subdivisions").val();
    if (subdivision == 0){
        $('#tanksModalWindow').modal('show');
        getSubdivisions();
    }
    else {
        var request = $.ajax({
            type: "POST",
            url: "/parser/tanks/data/",
            data:{"subdivision": subdivision}, //Временно указал явное значение подразделения
            cache: false
        });
        request.done(function (response) {
            //Подгружаю контент
            $("#tanks-content").html(response);
            $('#hidden-button').attr('hidden', false);
        });
    }
}
/**
 * Подгружает combo box с доступными подразделеними в модальное окно
 */
function getSubdivisions() {
    var request = $.ajax({
        type: "POST",
        url: "/parser/tanks/subdivisions/",
        cache: false
    });
    request.done(function (response) {
        $("#modal-content").html(response)
    });
}
/**
 * Слушатель нажатия на кнопку загрузки XML в модальном окне
 */
$('#parser-content').on('click' , '#modalUploadXmlButton', function () {
    var subdivision = $("#modal-subdivisions").val();
    var request = $.ajax({
        type: "POST",
        url: "/parser/tanks/data/",
        data:{"subdivision": subdivision}, //Временно указал явное значение подразделения
        cache: false
    });
    request.done(function (response) {
        //Подгружаю контент
        $("#tanks-content").html(response);
        $('#hidden-button').attr('hidden', false);
        $('#tanksModalWindow').modal('hide');
    });
});
