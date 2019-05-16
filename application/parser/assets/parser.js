/**
 * Created by Rain on 19.03.2019.
 */
/*
 * --------------------------------------------------------------------------------------------------------------------
 *                                      Public Variables и загрузка страницы
 * --------------------------------------------------------------------------------------------------------------------
 */
/**
 * Константы идентификаторы обработчиков хранилишь
 */
const FOLDER_CHECKER_ID = 1;
const MYSQL_CHECKER_ID = 2;
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
        case "outcomes": /*showOutcomesData();*/ $("#parser-outcomes").addClass('active'); break;
        case "incomes": showIncomesData(); break;
        case "office": showOfficeData(); break;
        default: showMainData(); $("#parser-main").addClass('active'); break; //Если состояние еще не установлено, будет подгружаться заданная страница
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
/**
 *
 */

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
        //Очистить
        $("div#parser-content").empty();
        //Добавляю секцию куда выгружу контент
        if ($("#parser-content").append('<div id="step-1"></div>')){
            //Подгружаю контент
            if ($("#step-1").html(response)){
                showPaginationPageData(1,FOLDER_CHECKER_ID);
                $(".page-item:first").addClass('active');
            }
            //Устанавливаю заголовок
            $("#title").text("Parser");
        }
    });
}
/**
 * Подгружает контент
 */
function showPaginationPageData(current_page, checker_id) {
    var request = $.ajax({
        type: "POST",
        url: "/parser/pagination/" + checker_id,
        data: {"current_page": current_page},
        cache: false
    });
    request.done(function (response) {
        //Очистить
        $("div#pagination-content").empty();
        //Выделение кнопки при нажатии на нее
        $(".page-item").on('click', function(){
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
        });
        //Добавляю секцию куда выгружу контент
        $("#pagination-content").html(response);
    });
}