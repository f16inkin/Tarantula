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
        case "reports":  $("#parser-reports").addClass('active'); break;
        case "controls": $("#parser-controls").addClass('active'); break;
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
$("#parser-reports").on("click", function () {
    //Установка состояния
    localStorage.setItem("parserState", "reports");
    //Выполнение AJAX запроса, загрузка контента
    alert('Отчеты');
});
/**
 * Обрабатывает нажатие на вкладку "Отпуск топлива".
 */
$("#parser-controls").on("click", function () {
    //Установка состояния
    localStorage.setItem("parserState", "controls");
    //Выполнение AJAX запроса, загрузка контента
    alert('Управление');
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
        //Очистить
        $("div#parser-content").empty();
        //Добавляю секцию куда выгружу контент
        if ($("#parser-content").html(response)){
        }
        $("#title").text("Parser");
    });
}

function showFirstStep() {
    var request = $.ajax({
        type: "POST",
        url: "/parser/first-step/",
        cache: false
    });
    request.done(function (response) {
        $("div#parser-content").empty();
        $("#parser-content").html(response);
        showPaginationPageData(1,FOLDER_CHECKER_ID);
        $(".page-item:first").addClass('active');
        $("#title").text("Проверка хранилища. Шаг-1");
    });
}

function deleteFilesFomDirectory() {
    var files = [];
    var strings = [];
    var box = $('.hidden-checkbox');
    box.filter(':checked').each(function() {
        files.push(this.value);
        var box2 = $(this).parent().parent().attr("id");
        strings.push(box2);
    });
    var request = $.ajax({
        type: "POST",
        url: "/parser/delete-files/",
        data: {"files": files},
        cache: false,
        beforeSend: function () {
            showFlashWindow('Удаление...', 'success_flash_window');
        },
        complete: function () {
            hideFlashWindow('success_flash_window');
        }
    });
    request.done(function () {
        //
        $.each(strings, function(index, value) {
            $("#"+value).css({'backgroundColor' : 'rgb(241, 186, 191)', 'border' : 'solid 1px', 'border-color' : '#f5c6cb'});
            $("#"+value).delay(500).fadeOut(500, function () {
                $(this).remove();
            });
        });
        var paginationData = function () {
           showPaginationPageData(1, FOLDER_CHECKER_ID);
        };
        setTimeout(paginationData, 1500); //Здесь устанавливается задержка перед подгрузкой контента пагинации
        buildPagination(FOLDER_CHECKER_ID);
        //Снимаю главный чекбокс
        $("#check_start").prop('checked', false);
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
        cache: false,
        beforeSend: function () {
            //showFlashWindow('Загрузка...', 'success_flash_window');
        },
        complete: function () {
            //hideFlashWindow('success_flash_window');
        }
    });
    request.done(function (response) {
        //Очистить
        $("#table-pagination-content").empty();
        //Выделение кнопки при нажатии на нее
        $(".page-item").on('click', function(){
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
        });
        //Добавляю секцию куда выгружу контент
        res = JSON.parse(response);
        $.each(res.page_data, function(key, value) {
            var line = $('<tr id="table_line_'+key+'" class="tr-table-content">' +
                    '<td>' +
                        '<input id="check_'+key+'" class="hidden-checkbox" type="checkbox" value="'+value+'"/>' +
                            ' <label for="check_'+key+'">' +
                                ' <div><i class="fa fa-check"></i></div>' +
                            '</label>' +
                    '</td>' +
                    '<td>'+value+'</td>' +
                '</tr>').css({'backgroundColor': 'rgb(241, 186, 191)', 'border' : 'solid 1px', 'border-color' : '#f5c6cb'}).hide().fadeIn(1000);
            $("#table-pagination-content").append(line);
        });
        //Выставляю лимит и количество файлов
        var files_limit = res.files_limit;
        var files_count = res.files_count;
        $(".alert-warning > b").text(files_count+'/'+files_limit+' шт.');
    });
}


/**
 * Всплывающее окно. Показать / Убрать
 */
function showFlashWindow(message, window) {
    $('#wrapper').prepend('<div class="'+window+'">'+message+'</div>');
}
function hideFlashWindow(window) {
    $("."+window).delay(500).fadeOut(500, function () {
        $(this).remove();
    });
}

/**
 * Чекбоксы в таблице в первом шаге
 */
$("#parser-content").on('click', '#check_start', function () {
    $("input[type=checkbox]").prop('checked', $(this).prop('checked'));
});

/**
 * Пагинация
 */
function buildPagination(checker_id) {
    var request = $.ajax({
        type: "POST",
        url: "/parser/pagination/build/" + checker_id,
        cache: false
    });
    request.done(function (response) {
        //Очистить
        $("div#pagination").empty();
        //Добавляю секцию куда выгружу контент
        $("#pagination").html(response);
        //Выбеляю кнопку первой страницы
        $(".page-item:first").addClass('active');
    });
}
