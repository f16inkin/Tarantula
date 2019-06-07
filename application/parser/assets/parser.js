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
/**
 * Часто используемые в обращении статические элементы
 */
const parser_content = $('#parser-content');        //рабочая область парсера
const parser_main = $('#parser-main');              //кнопка "Главная"
const parser_reports = $('#parser-reports');        //кнопка "Отчеты
const parser_controls = $('#parser-controls');      //кнопка "Управление"
const title = $('#title');                          //заголовок
/*
 * Загрузка страницы
 * Каждый раз при перезагрузке страницы, браузер будет подгружать через AJAX именно ту часть которая была подгружена
 * до перезагрузки. Достигается это засчет сканирования состояния которое я устанвливаю как ключ - значение в
 * localStorage браузера.
 */
$(function () {
    let state = localStorage.getItem("parserState");
    switch (state){
        case "main": showMainData(); parser_main.addClass('active'); break;
        case "reports":  parser_reports.addClass('active'); break;
        case "controls": parser_controls.addClass('active'); break;
        default: showMainData(); parser_main.addClass('active'); break; //Если состояние еще не установлено, будет подгружаться заданная страница
    }
});
/*
 * --------------------------------------------------------------------------------------------------------------------
 *                                          Функции обработчики событий
 * --------------------------------------------------------------------------------------------------------------------
 */

/**
 * Обрабатывает нажатие на вкладку "Главная"
 */
parser_main.on('click', function () {
    //Установка состояния
    localStorage.setItem('parserState', 'main');
    //Выполнение AJAX запроса, загрузка контента
    showMainData();
});
/**
 * Обрабатывает нажатие на вкладку "Отчеты"
 */
parser_reports.on('click', function () {
    //Установка состояния
    localStorage.setItem('parserState', 'reports');
    //Выполнение AJAX запроса, загрузка контента
    alert('Отчеты');
});
/**
 * Обрабатывает нажатие на вкладку "Управление"
 */
parser_controls.on('click', function () {
    //Установка состояния
    localStorage.setItem('parserState', 'controls');
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
        //Очистить. Можно и не очищать так как html заного рисует страницу
        parser_content.empty();
        //Добавляю секцию куда выгружу контент
        parser_content.html(response);
        //Устанавливаю заголовок
        title.text('Parser');
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
        //Очистить рабочую оласть, работает и без очистки
        parser_content.empty();
        //Загрузить разметку страницы
        parser_content.html(response);
        //Загружаю строки файлов в таблицу
        showPaginationPageData(1, FOLDER_CHECKER_ID);
        //Активирую первую кнопку навигатора
        //setUpPagination(1, 500);
        buildPagination(FOLDER_CHECKER_ID, 1);
        //Установка титула старницы
        title.text('Проверка хранилища. Шаг-1');
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
    let current_page = $('li.active').text();
    /*------------------------*/
    check_boxes.filter(':checked').each(function () {
        file_names.push(this.value);
        let table_row_id = $(this).parent().parent().attr('id');
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
                let table_row = $('#' + value);
                table_row.css({
                    'backgroundColor': 'rgb(241, 186, 191)',
                    'border': 'solid 1px',
                    'border-color': 'rgb(251, 136, 148)'
                });
                table_row.delay(500).fadeOut(500, function () {
                    $(this).remove();
                });
            });
            res = JSON.parse(response);
            let files = res.uploaded_files.data;
            let files_limit = res.files_limit;
            let files_count = res.files_count;
            let page = res.uploaded_files.page;
            let build_pagination = res.uploaded_files.build;
            const uploadFiles = function () {
                filesUpload(files, files_count, files_limit, page);
            };
            setTimeout(uploadFiles, 1500);
            //Если в ответе есть команда строить заного навигатор, то выполняю функцию, которая требует еще один
            //запрос к серверу
            if (build_pagination){
                //Настройка пагинатора
                //setUpPagination(page, 500);
                buildPagination(FOLDER_CHECKER_ID, page);
            }

            //Снимаю главный чекбокс
            $('#check_start').prop('checked', false);
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
 * @param files
 * @param files_count
 * @param files_limit
 */
function filesUpload(files, files_count, files_limit) {
    $.each(files, function(key, value) {
        let unique_id = value.replace('.','');
        let line =
            $(`<tr id='table_line_${unique_id}' class='tr-table-content'>` +
                `<td>` +
                `<input id='check_${unique_id}' class='hidden-checkbox checkable' type='checkbox' value='${value}'/>` +
                `<label for='check_${unique_id}'>` +
                `<div><i class='fa fa-check'></i></div>` +
                `</label>` +
                `</td>` +
                `<td>${value}</td>` +
                `</tr>`).hide().fadeIn(1000);
        $('#table-pagination-content').append(line);
    });
    //Выставляю лимит и количество файлов
    $('.alert-warning > b').text(files_count+'/'+files_limit+' шт.');
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
        $('#table-pagination-content').empty();
        res = JSON.parse(response);
        $.each(res.page_data, function(key, value) {
            let unique_id = value.replace('.','');
            let line =
                $(`<tr id='table_line_${unique_id}' class='tr-table-content'>` +
                    `<td>` +
                        `<input id='check_${unique_id}' class='hidden-checkbox checkable' type='checkbox' value='${value}'/>` +
                            `<label for='check_${unique_id}'>` +
                                `<div><i class='fa fa-check'></i></div>` +
                            `</label>` +
                    `</td>` +
                    `<td>${value}</td>` +
                `</tr>`).hide().fadeIn(1000);
            $('#table-pagination-content').append(line);
        });
        //Выставляю лимит и количество файлов
        let files_limit = res.files_limit;
        let files_count = res.files_count;
        $('.alert-warning > b').text(files_count+'/'+files_limit+' шт.');
        //При переходе по страницам выделяет элементы, если выделен главный чекюокс
        $('input[type=checkbox]').prop('checked', $('#check_start').prop('checked'));
    });
}

/**
 * Показывает всплывающее окно
 * ---------------------------
 * @param {string} message
 * @param {string} window
 */
function showFlashWindow(message, window) {
    $('#wrapper').prepend(`<div class=${window}>${message}</div>`);
}

/**
 * Скрывает всплывающее окно
 * -------------------------
 * @param {string} window
 */
function hideFlashWindow(window) {
    $('.'+window).delay(500).fadeOut(500, function () {
        $(this).remove();
    });
}

/**
 * Чекбоксы в таблице в первом шаге
 */
parser_content.on('click', '#check_start', function () {
    $('input[type=checkbox]').prop('checked', $(this).prop('checked'));
});

/**
 * Обрабатывает нажатия на кнопки постраничной навигации
 */
parser_content.on('click', '.page-item', function () {
   let page = $(this).text();
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
   showPaginationPageData(page, FOLDER_CHECKER_ID);
});

/**
 * Создает навигатор. И активирует выбранную кнопку
 * ------------------------------------------------
 * @param {string} checker_id
 * @param {number} page
 */
function buildPagination(checker_id, page) {
    let request = $.ajax({
        type: "POST",
        url: "/parser/pagination/get-pages-count/" + checker_id,
        cache: false
    });
    request.done(function (response) {
        //Очистить
        $('ul#pagination-list').empty();
        //Добавляю секцию куда выгружу контент
        for (let i = 1; i < +response + 1; i++) {
            let line = `<li id='page_${i}' class='page-item'><a class='page-link'>${i}</a></li>`;
            $('#pagination-list').append(line);
        }
        $('#page_'+page).addClass('active');
    });
}

/**
 * Активирует кнопку навигатора, через установленное timeout время, сразу после его построения. buildPagination.
 * -------------------------------------------------------------------------------------------------------------
 * @param {number} page
 * @param {number} timeout
 */
const setUpPagination = function(page, timeout){
    buildPagination(FOLDER_CHECKER_ID);
    setTimeout(function () {
        $('#page_'+page).addClass('active');
    }, timeout);
};
