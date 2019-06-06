/**
 * Created by Rain on 19.03.2019.
 */
/*
 * --------------------------------------------------------------------------------------------------------------------
 *                                      Public Variables и загрузка страницы
 * --------------------------------------------------------------------------------------------------------------------
 */
/**
 * Константы идентификаторы обработчиков хранилищь
 */
const FOLDER_CHECKER_ID = 'folder';
const MYSQL_CHECKER_ID = 'mysql';
/*
 * Загрузка страницы
 * Каждый раз при перезагрузке страницы, браузер будет подгружать через AJAX именно ту часть которая была подгружена
 * до перезагрузки. Достигается это засчет сканирования состояния которое я устанвливаю как ключ - значение в
 * localStorage браузера.
 */
$(function () {
    let state = localStorage.getItem("parserState");
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
const showMainData = function () {
    let request = $.ajax({
        type: "POST",
        url: "/parser/main",
        cache: false
    });
    request.done(function (response) {
        //Очистить
        $("div#parser-content").empty();
        //Добавляю секцию куда выгружу контент
        if ($("#parser-content").html(response)) {
        }
        $("#title").text("Parser");
    });
};

/**
 * Подгружает контент для первого шага
 */
const showFirstStep = function () {
    let request = $.ajax({
        type: "POST",
        url: "/parser/first-step",
        cache: false
    });
    request.done(function (response) {
        //Очистить рабочую оласть
        $("div#parser-content").empty();
        //Загрузить разметку страницы
        $("#parser-content").html(response);
        //Загружаю строки файлов в таблицу
        showPaginationPageData(1, FOLDER_CHECKER_ID);
        //filesUpload(0, 1, FOLDER_CHECKER_ID);
        buildPagination(FOLDER_CHECKER_ID);
        //Делаю активным первую кнопку пагинатора
        //$(".page-item:first").addClass('active');
        //Установка титула старницы
        $("#title").text("Проверка хранилища. Шаг-1");
    });

};

/**
 * Шаг №1. Удаляет строки/файлы из пользовательской директории/таблицы
 */
const deleteFilesFromDirectory = function (){
    let file_names = [];    //Имена файлов из директории + расширение
    let table_rows = [];    //Массив с id строк таблицы
    let check_boxes = $('.checkable');
    /*------------------------*/
    let current_page = $("li.active").text();
    console.log(current_page);
    /*------------------------*/
    check_boxes.filter(':checked').each(function () {
        file_names.push(this.value);
        let table_row_id = $(this).parent().parent().attr("id");
        table_rows.push(table_row_id);
    });
    if (file_names.length > 0) {
        /**
         * AJAX action
         */
        let request = $.ajax({
            type: "POST",
            url: "/parser/pagination/delete-and-upload/" + FOLDER_CHECKER_ID,
            data: {"file_names": file_names, "quantity": file_names.length, "current_page": current_page},
            cache: false,
            beforeSend: function () {
                showFlashWindow('Удаление...', 'success_flash_window');
            },
            complete: function () {
                hideFlashWindow('success_flash_window');
            }
        });
        request.done(function (response) {
            //Пошагово убираю удаленные строки из таблицы
            $.each(table_rows, function (index, value) {
                let table_row = $("#" + value);
                table_row.css({
                    'backgroundColor': 'rgb(241, 186, 191)',
                    'border': 'solid 1px',
                    'border-color': 'rgb(251, 136, 148)'
                });
                table_row.delay(500).fadeOut(500, function () {
                    $(this).remove();
                });
            });
            const uploadFiles = function () {
                filesUpload(response);
            };
            setTimeout(uploadFiles, 1500);
            //Настройка пагинатора
            buildPagination(FOLDER_CHECKER_ID);
            //Снимаю главный чекбокс
            $("#check_start").prop('checked', false);
        });
    } else {
        showFlashWindow('Выберите файлы для удаления', 'success_flash_window');
        let func_hide = function () {
            hideFlashWindow('success_flash_window')
        };
        setTimeout(func_hide, 1000);
    }
};

/**
 * Подгружает строки файлов
 * ------------------------
 * @param response
 */
function filesUpload(response) {
    res = JSON.parse(response);
    $.each(res.uploaded_files.data, function(key, value) {
        let unique_id = value.replace('.',"");
        let line =
            $(`<tr id='table_line_${unique_id}' class="tr-table-content">` +
                `<td>` +
                `<input id='check_${unique_id}' class='hidden-checkbox checkable' type='checkbox' value='${value}'/>` +
                `<label for='check_${unique_id}'>` +
                `<div><i class='fa fa-check'></i></div>` +
                `</label>` +
                `</td>` +
                `<td>${value}</td>` +
                `</tr>`).hide().fadeIn(1000);
        $("#table-pagination-content").append(line);
    });
    //Выставляю лимит и количество файлов
    let files_limit = res.files_limit;
    let files_count = res.files_count;
    $(".alert-warning > b").text(files_count+'/'+files_limit+' шт.');
    //Выделение страницы
    let page = res.uploaded_files.page;
    $("#page_"+page).addClass('active');
}

/**
 * Загружает полную страницу файлов
 * --------------------------------
 * @param current_page
 * @param checker_id
 */
function showPaginationPageData(current_page, checker_id) {
    let request = $.ajax({
        type: "POST",
        url: "/parser/pagination/" + checker_id,
        data: {"current_page": current_page},
        cache: false
    });
    request.done(function (response) {
        //Очистить
        $("#table-pagination-content").empty();
        res = JSON.parse(response);
        $.each(res.page_data, function(key, value) {
            let unique_id = value.replace('.',"");
            let line =
                $(`<tr id='table_line_${unique_id}' class="tr-table-content">` +
                    `<td>` +
                        `<input id='check_${unique_id}' class='hidden-checkbox checkable' type='checkbox' value='${value}'/>` +
                            `<label for='check_${unique_id}'>` +
                                `<div><i class='fa fa-check'></i></div>` +
                            `</label>` +
                    `</td>` +
                    `<td>${value}</td>` +
                `</tr>`).hide().fadeIn(1000);
            $("#table-pagination-content").append(line);
        });
        //Выставляю лимит и количество файлов
        let files_limit = res.files_limit;
        let files_count = res.files_count;
        $(".alert-warning > b").text(files_count+'/'+files_limit+' шт.');
    });
}

/**
 * Показывает всплывающее окно
 * ---------------------------
 * @param message
 * @param window
 */
function showFlashWindow(message, window) {
    $('#wrapper').prepend('<div class="'+window+'">'+message+'</div>');
}

/**
 * Скрывает всплывающее окно
 * -------------------------
 * @param window
 */
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
 * Обрабатывает нажатия на кнопки постраничной навигации
 */
$("#parser-content").on('click', '.page-item', function () {
   let page = $(this).text();
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
   showPaginationPageData(page, FOLDER_CHECKER_ID);
});

/**
 * Пагинация
 */
function buildPagination(checker_id) {
    let request = $.ajax({
        type: "POST",
        url: "/parser/pagination/get-pages-count/" + checker_id,
        cache: false
    });
    request.done(function (response) {
        //Очистить
        $("ul#pagination-list").empty();
        //Добавляю секцию куда выгружу контент
        for (let i = 1; i < +response+1 ; i++) {
            let line = ' <li id="page_'+i+'" class="page-item"><a class="page-link">'+i+'</a></li>';
            $("#pagination-list").append(line);
        }
        //$(".page-item:first").addClass('active');
    });
}
