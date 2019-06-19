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
 * Подгружает контент для первого шага. Только основную разметку без внутреннего содержимого.
 * Содержание определаяется в контроллере и подгружается в респонсе. Дальнейшие действия определяются скриптом
 * подгруженной страницы
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
    });

};

/**
 * Шаг №1. Удаляет строки/файлы из пользовательской директории/таблицы
 */
const deleteFiles = function (){
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
            url: "/parser/pagination/displace/" + FOLDER_CHECKER_ID,
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
            let files = res.data.uploaded_files.data;
            let files_limit = res.data.files_limit;
            let files_count = res.data.files_count;
            let page = res.data.uploaded_files.page;
            let build_pagination = res.data.uploaded_files.build;
            const loadFiles = function () {
                filesLoad(files, files_count, files_limit);
            };
            setTimeout(loadFiles, 1500);
            //Если в ответе есть команда строить заного навигатор, то выполняю функцию, которая требует еще один
            //запрос к серверу
            if (build_pagination){
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
 * @param {int} files_count
 * @param {int} files_limit
 */
const filesLoad = function (files, files_count, files_limit) {
    $.each(files, function (key, value) {
        let unique_id = value.replace('.', '');
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
    $('.alert-primary > b').text(`${files_count} - доступно / ${files_limit} - лимит разовой обработки.`);
    /**
     * Если вернулось после подгрузки 0 файлов, то тогда отображаю элементы загрузки файлов.
     * Делаю через функцию firstStep, как дополнительная проверка того, что в промежутке между нажатием на кнопку, в
     * директории не появились файлы. Если так, то парсер снова подгрузит страницу обработки файлов
     */

    if (files_count === 0){
        setTimeout(showFirstStep, 1000);
    }
};

/**
 * Загружает страницу с файлами
 * --------------------------------
 * @param {int} current_page
 * @param {string} checker_id
 */
function loadPage(current_page, checker_id) {
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
        $('.alert-primary > b').text(files_count+'/'+files_limit+' шт.');
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
   loadPage(page, FOLDER_CHECKER_ID);
});

/**
 * Создает навигатор. И активирует выбранную кнопку
 * ------------------------------------------------
 * @param {string} checker_id
 * @param {int} page
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
 * Загружает файлы в папку пользователя
 * ------------------------------------
 * @returns {boolean}
 */
function reportsUpload() {
    let form = $('#upload-reports-form');
    let formData = new FormData();
    let input = form.find('input');
    let files_container = parser_content.find($('#files_container'));
    /**
     * Если загружаются файлы, то каждый загружается в массв
     */
    if((input[0].files).length != 0) {
        $.each(input[0].files, function (i, file) {
            formData.append(`file[${i}]`, file);
        });
    /**
     * Овопевещние о том, что не выбраны файлы для загрузки
     */
    }else {
        let card = parser_content.find($('.card'));
        card.empty();
        files_container.empty();
        let alert = `<div class='alert alert-danger'  role='alert'>Выберите файлы для загрузки</div>`;
        card.prepend(alert);
        return false;
    }
    /**
     * AJAX action
     */
    let request = $.ajax({
        type: 'POST',
        url: '/parser/uploader/upload',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        /**
         * Блокирую кнопки загрузки и выбора файлов, до того как запрос начал выполнятся. Защита от дабл клика и тд.
         */
        beforeSend: function () {
            //input.prop('disabled', true);
            //button.attr('disabled', true);
        },
        /**
         * При удачном выполнении ajax запроса, добавляю кнопки Обработать и Загрузить в навигацию
         */
        success: function(){
            let parser_nav_bar = parser_content.find($('.parser-nav-bar'));
            parser_nav_bar.empty();
            files_container.empty();
            let select_button =
                `<div class="parser-nav-bar-container">
                    <label for='upload-reports' class='btn btn-warning btn-sm'>
                    <span class='fa fa-folder-open'></span> Выбрать файлы</label>
                    <input id='upload-reports' type='file' name='reports[]' multiple='multiple' onchange='fillFileContainer(); return false;'/>
                </div>`;
            let handle_button =
                `<div class="parser-nav-bar-container">
                    <button class='btn btn-primary btn-sm' onclick='showFirstStep(); return false;'>
                    <i class='fa fa-chevron-circle-right' aria-hidden='true'></i> Обработать</button>
                </div>`;
            let upload_button =
                `<div class="parser-nav-bar-container">
                    <button class='btn btn-success btn-sm' onclick='reportsUpload(); return false;'>
                    <i class='fa fa-upload' aria-hidden='true'></i> Загрузить</button>
                </div>`;
            parser_nav_bar.prepend(select_button);
            parser_nav_bar.append(upload_button);
            parser_nav_bar.append(handle_button);

        },
        /**
         * После выполнения ajax запроса (с любым результатом success / error) разблокирую кнопки загрузки и выбора файлов
         */
        complete: function () {
            //input.prop('disabled', false);
            //button.attr('disabled', false);
            //input.val('');
        }
    });
    /**
     * После того как запрос отработал. Обрабатываю сообщение о каждом файле и вывожу алерты с результатом выполнения.
     */
    request.done(function (response) {
        //Очистить рабочую область
        let card = parser_content.find($('.card'));
        card.empty();
        let res = JSON.parse(response);
        //Для каждого сообщения пришедшего в респонсе
        $.each(res.executionResult, function(key, value){
            switch (value.status) {
                case 'success' : alert_type = 'success'; break;
                case 'warning' : alert_type = 'warning'; break;
                case 'fail' : alert_type = 'danger'; break;
                default : alert_type = 'success';
            }
            //Вывожу алерт сообщение с результатом
            let line = `<div class='alert alert-${alert_type}' style='padding: 5px;' role='alert'>${value.message}</div>`;
            card.append(line);

        });
        //Информационный алерт
        let files_count = res.executionResult.length;
        let information = `<div class='alert alert-primary' role='alert'><b>Обработано <span class="badge badge-light">${files_count}</span> файлов</b></div>`;
        card.prepend(information);
        //Заголовок
        title.text('Загрузка файлов. Шаг-1');
    });
}

/**
 * Заполняет контейнер файлами которые готовятся к загрузке
 */
function fillFileContainer() {
    let form = $('#upload-reports-form');
    let input = form.find('input');
    let files_place = $('#parser-content').find($('#files_container'));
    let card = parser_content.find($('.card'));
    let files = input[0].files;
    files_place.empty();
    card.empty();
    $.each(files, function(key, value){
       let file_name = value.name;
       let file_extension = file_name.split(".").pop();
       let single_file =
           `<div class='single_file' title='${file_name}'>
                <i class="fa fa-folder"></i> ${file_name}
            </div>`;
       files_place.append(single_file);
    });
    let alert =`<div class='alert alert-primary'  role='alert'><b>Выбрано</b> <span class="badge badge-light">${files.length}</span> <b>файлов</b></div>`;
    card.append(alert);
}

/**
 * Инициализирует линию прогресса.
 */
function initProgressLine(){
    let activePosition = $('.slide span.active').position();
    let currentPosition = ($('#stage_1').position()).left;
    activePosition = activePosition.left-currentPosition;
    $(".after").stop().animate({width: activePosition + "px"});
}

/**
 * Переключает классы на значках этапво active/inactive
 * ----------------------------------------------------
 * @param {string} stage
 */
function toggleStage(stage) {
    let slides = parser_content.find($('.slide span'));
    //Добавляю класс неактивного эатапа на каждый значек
    $.each(slides, function () {
        $(this).addClass('inactive');
    });
    //Чтобы работала линия прогрузки. Переделать потом
    $('.slide span').removeClass('active').siblings().addClass('inactive');
    //Активирую указанный значек
    $('#'+stage).removeClass('inactive').addClass('active');
}