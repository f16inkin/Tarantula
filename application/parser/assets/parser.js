/**
 * Created by Rain on 19.03.2019.
 */
/*
 * --------------------------------------------------------------------------------------------------------------------
 *                                      Public Variables и загрузка страницы
 * --------------------------------------------------------------------------------------------------------------------
 */

/**
 * Общие константы
 */
const PAGES_LIMIT = 10;
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
/**
 * Обрабатывает нажатие на первую кнопку "Линии прогресса"
 */
parser_content.on('click', '#stage_1', function () {
    firstStep();
});
/**
 * Обрабатывает нажатие на вторую кнопку "Линии прогресса"
 */
parser_content.on('click', '#stage_2', function () {
    secondStep();
});
/**
 * Обрабатывает нажатие на третью кнопку "Линии прогресса"
 */
parser_content.on('click', '#stage_3', function () {
    thirdStep();
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
        title.text('Parser XML');
    });
};


const getStarted = function () {
    /*let request = $.ajax({
        type: "POST",
        url: "/parser/get-started",
        cache: false
    });
    request.done(function (response) {
        //Очистить рабочую область, работает и без очистки
        parser_content.empty();
        //Загрузить разметку страницы
        parser_content.html(response);
    });*/
    let stage = localStorage.getItem("progressStage");
    switch (stage) {
        case "stage_1": firstStep(); break;
        case "stage_2": secondStep(); break;
        case "stage_3": thirdStep(); break;
        default: firstStep(); break;
    }
};

/**
 * Шаг №1
 * ------------------
 * @returns {boolean}
 */
const firstStep = function () {
    let request = $.ajax({
        type: "POST",
        url: "/parser/progress-line/first-step",
        cache: false
    });
    request.done(function (response) {
        //Очистить рабочую область, работает и без очистки
        parser_content.empty();
        //Загрузить разметку страницы
        parser_content.html(response);
    });
    localStorage.setItem('progressStage', 'stage_1');
    return false;
};

/**
 * Шаг №2
 * ------------------
 * @returns {boolean}
 */
const secondStep = function () {
    let request = $.ajax({
        type: "POST",
        url: "/parser/progress-line/second-step",
        cache: false
    });
    request.done(function (response) {
        //Очистить рабочую область, работает и без очистки
        parser_content.empty();
        //Загрузить разметку страницы
        parser_content.html(response);
    });
    localStorage.setItem('progressStage', 'stage_2');
    return false;
};
/**
 * Шаг №3
 * ------------------
 * @returns {boolean}
 */
const thirdStep = function () {
    let request = $.ajax({
        type: "POST",
        url: "/parser/progress-line/third-step",
        cache: false
    });
    request.done(function (response) {
        //Очистить рабочую область, работает и без очистки
        parser_content.empty();
        //Загрузить разметку страницы
        parser_content.html(response);
    });
    localStorage.setItem('progressStage', 'stage_3');
    return false;
};

/**
 *
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
            url: "/parser/inspector/displace",
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
            let files = res.page_data;
            let files_limit = res.files_limit;
            let files_count = res.files_count;
            let page = res.page;
            let build_pagination = res.build;
            const loadFiles = function () {
                filesLoad(files, files_count, files_limit);
            };
            setTimeout(loadFiles, 1500);
            //Если в ответе есть команда строить заного навигатор, то выполняю функцию, которая требует еще один
            //запрос к серверу
            if (build_pagination){
                buildPagination(page);
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
        let unique_id = value.file_name.replace('.','');
        let fileName = value.file_name;
        let sessionNumber = '-';
        let operator = '-';
        let sessionStart = '-';
        let sessionEnd = '-';
        switch (value.session.Status) {
            case 'correct' :
                text_type = 'success';
                text_icon = 'fa fa-check-circle';
                text_status = 'Корректный';
                break;
            case 'incorrect' :
                text_type = 'danger';
                text_icon = 'fa fa-times-circle';
                text_status = 'Не корректный';
                break;
        }
        if (value.session.Number != null){
            sessionNumber = value.session.Number;
            operator = value.session.Operator;
            sessionStart = value.session.StartDateTime;
            sessionEnd = value.session.EndDateTime;
        }
        let line =
            $(`<tr id='table_line_${unique_id}' class='tr-table-content'>
                    <td>
                        <input id='check_${unique_id}' class='hidden-checkbox checkable' type='checkbox' value='${value.file_name}'/>
                            <label for='check_${unique_id}'>
                                <div><i class='fa fa-check'></i></div>
                            </label>
                    </td>
                    <td>
                         <button class="btn btn-outline-primary btn-sm" onclick="loadSessionData('${fileName}')">
                             <i class="fa fa-search" aria-hidden="true"></i> ${fileName}
                        </button> 
                    </td>
                    <td>${sessionNumber}</td>
                    <td>${sessionStart}</td>
                    <td>${sessionEnd}</td>
                    <td>${operator}</td>
                    <td>
                        <div class='text-${text_type}'>
                            <i class='${text_icon}'></i> ${text_status}
                        </div>
                    </td>
                </tr>`).hide().fadeIn(1000);
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
        setTimeout(getStarted, 1000);
    }
};

/**
 * Загружает страницу с файлами
 * --------------------------------
 * @param {int} current_page
 */
function loadPage(current_page) {
    let request = $.ajax({
        type: "POST",
        url: "/parser/inspector/inspect",
        data: {"current_page": current_page},
        cache: false
    });
    request.done(function (response) {
        //Очистить
        $('#table-pagination-content').empty();
        res = JSON.parse(response);
        $.each(res.page_data, function(key, value) {
            let unique_id = value.file_name.replace('.','');
            let fileName = value.file_name;
            let sessionNumber = '-';
            let operator = '-';
            let sessionStart = '-';
            let sessionEnd = '-';
            switch (value.session.Status) {
                case 'correct' :
                    text_type = 'success';
                    text_icon = 'fa fa-check-circle';
                    text_status = 'Корректный';
                    break;
                case 'incorrect' :
                    text_type = 'danger';
                    text_icon = 'fa fa-times-circle';
                    text_status = 'Не корректный';
                    break;
            }
            if (value.session.Number != null){
                sessionNumber = value.session.Number;
                operator = value.session.Operator;
                sessionStart = value.session.StartDateTime;
                sessionEnd = value.session.EndDateTime;
            }
            let line =
                $(`<tr id='table_line_${unique_id}' class='tr-table-content'>
                    <td>
                        <input id='check_${unique_id}' class='hidden-checkbox checkable' type='checkbox' value='${value.file_name}'/>
                            <label for='check_${unique_id}'>
                                <div><i class='fa fa-check'></i></div>
                            </label>
                    </td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm" onclick="loadSessionData('${fileName}')">
                             <i class="fa fa-search" aria-hidden="true"></i> ${fileName}
                        </button> 
                    </td>
                    <td>${sessionNumber}</td>
                    <td>${sessionStart}</td>
                    <td>${sessionEnd}</td>
                    <td>${operator}</td>
                    <td>
                        <div class='text-${text_type}'>
                            <i class='${text_icon}'></i> ${text_status}
                        </div>
                    </td>
                </tr>`).hide().fadeIn(1000);
            $('#table-pagination-content').append(line);
        });
        //Выставляю лимит и количество файлов
        let files_limit = res.files_limit;
        let files_count = res.files_count;
        $('.alert-primary > b').text(`${files_count} - доступно / ${files_limit} - лимит разовой обработки.`);
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
parser_content.on('click', '.page-button', function () {
   let page = $(this).text();
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
   loadPage(page);
});

parser_content.on('click', '.next-pages-button', function () {
    let request = $.ajax({
        type: "POST",
        url: "/parser/inspector/get-pages-count",
        cache: false
    });
    request.done(function (response) {
        //Последняя страница в данный момент
        let page = $("li.page-button").last().text();
        //Начальаня точка навигации
        let first_page = +page + 1;
        //Конечная кнопка навигации
        let last_page = +page + PAGES_LIMIT;
        let pagination_list = $('#pagination-list');
        //Кнопки
        let previous_button = `<li class="page-item"><a class="page-link previous-pages-button" tabindex="-1">Предыдущая</a></li>`;
        let next_button = `<li class="page-item"><a class="page-link next-pages-button" tabindex="-1">Следующая</a></li>`;
        if (last_page > response){
            last_page = response;
        }
        //Полная зачитска навигатора, для перерисовки нового
        pagination_list.empty();
        //Построение новго навигатора
        for (let i = first_page; i <= last_page; i++) {
            let line = `<li id='page_${i}' class='page-item page-button'><a class='page-link'>${i}</a></li>`;
            pagination_list.append(line);
        }
        //let marker_page = $("li.page-button").first().text();
        if (response > last_page){
            pagination_list.append(next_button);
        }
        if (first_page > PAGES_LIMIT){
            pagination_list.prepend(previous_button);
        }
        $('#page_' + first_page).addClass('active');
        loadPage(first_page);
    });

});

parser_content.on('click', '.previous-pages-button', function () {
    let request = $.ajax({
        type: "POST",
        url: "/parser/inspector/get-pages-count",
        cache: false
    });
    request.done(function (response) {
        //Последняя страница в данный момент
        let page = $("li.page-button").first().text();
        //Начальаня точка навигации
        let first_page = +page - PAGES_LIMIT;
        //Конечная кнопка навигации
        let last_page = +page - 1;
        let pagination_list = $('#pagination-list');
        //Кнопки
        let previous_button = `<li class="page-item"><a class="page-link previous-pages-button" tabindex="-1">Предыдущая</a></li>`;
        let next_button = `<li class="page-item"><a class="page-link next-pages-button" tabindex="-1">Следующая</a></li>`;
        if (last_page > response){
            last_page = response;
        }
        //Полная зачитска навигатора, для перерисовки нового
        pagination_list.empty();
        //Построение новго навигатора
        for (let i = first_page; i <= last_page; i++) {
            let line = `<li id='page_${i}' class='page-item page-button'><a class='page-link'>${i}</a></li>`;
            pagination_list.append(line);
        }
        //let marker_page = $("li.page-button").first().text();
        if (response > last_page){
            pagination_list.append(next_button);
        }
        if (first_page > PAGES_LIMIT){
            pagination_list.prepend(previous_button);
        }
        $('#page_' + first_page).addClass('active');
        loadPage(first_page);
    });

});

/**
 * Создает навигатор. И активирует выбранную кнопку
 * ------------------------------------------------
 * @param {int} page
 */
function buildPagination(page) {
    let request = $.ajax({
        type: "POST",
        url: "/parser/inspector/get-pages-count",
        cache: false
    });
    request.done(function (response) {
        if (response > 0){
            //Первая страница в стеке кнопок навигации
            let first_page = $("li.page-button").first().text();
            //Последняя страница в данный момент
            let last_page = +first_page + PAGES_LIMIT - 1;//$("li.page-button").last().text();
            //Начальаня точка навигации
            //Кнопки
            let previous_button = `<li class="page-item"><a class="page-link previous-pages-button" tabindex="-1">Предыдущая</a></li>`;
            let next_button = `<li class="page-item"><a class="page-link next-pages-button" tabindex="-1">Следующая</a></li>`;
            //Если конечная кнопка навигации указывает на страницу, большую чем есть на самом деле
            if (last_page > response){
                last_page = response;
            }
            //Маленький фикс, проблем с определением количества страниц
            if (first_page == ''){
                first_page = 1;
                last_page = PAGES_LIMIT;
            }
            if (first_page > response){
                first_page = last_page - PAGES_LIMIT + 1;
            }
            //Зачистка пагинации
            $('#pagination-list').empty();

            for (let i = first_page; i < +last_page + 1; i++) {
                let line = `<li id='page_${i}' class='page-item page-button'><a class='page-link'>${i}</a></li>`;
                $('#pagination-list').append(line);
            }
            if (response > last_page){
                $('#pagination-list').append(next_button);
            }
            if (first_page > PAGES_LIMIT){
                $('#pagination-list').prepend(previous_button);
            }
            $('#page_' + page).addClass('active');
            console.log(first_page + ' first_page');
            console.log(last_page + ' last_page');
            console.log('-------------------------');
        }
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
    let button = form.find('button');
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
            button.attr('disabled', true);
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
                    <button class='btn btn-primary btn-sm' onclick='secondStep(); return false;'>
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
            button.attr('disabled', false);
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
    let files_place = parser_content.find($('#files_container'));
    let card = parser_content.find($('.card'));
    let files = input[0].files;
    files_place.empty();
    card.empty();
    $.each(files, function(key, value){
       let file_name = value.name;
       let file_extension = file_name.split(".").pop();
       let single_file =
           `<div class='single_file' title='${file_name}'>
                <i class="fa fa-file"></i> ${file_name}
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

/**
 * Загружает информацию по смене, емкостям, отпуску, приему считанную из XML файла
 * -------------------------------------------------------------------------------
 * @param {string} file_name
 */
function loadSessionData(file_name) {
    let subdivision_id = 4;
    let request = $.ajax({
        type: "POST",
        url: "/parser/get-session-data",
        data: {'subdivision_id' : subdivision_id, 'file_name' : file_name},
        cache: false,
        beforeSend:function(){
            //showFlashWindow('Загрузка', 'success_flash_window');
        },
        success:function () {
            //hideFlashWindow('success_flash_window');
        }
    });
    request.done(function (response) {
        //очищаю модальное окно
        $('#modal-content').empty();
        //Отображаю его
        $('#exampleModalCenter').modal('show');
        //Загрузить разметку страницы
        $('#modal-content').append(response);
        $('#exampleModalLongTitle').text('Файл: '+file_name);

    });
}

function uploadToDatabase() {

}