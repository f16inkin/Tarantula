<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 11:25
 */
use application\parser\controllers\ControllerPagination;
?>
<div class="card">
    <div class="alert alert-danger" role="alert">
        Количество файлов в директории превышает допустимое. Найдено <b><?=$content['files_count'];?> шт.</b> из <b><?=$content['files_limit'];?> шт.</b> допустимых.
        Для продолжения работы парсера, необходимо удалить <b><u>лишние файлы.</u></b>
    </div>
    <div class="card-header" id="headingOne" style="margin-top: -1rem;">
        <h5 class="mb-0">
            <button class="btn btn-secondary btn-sm" data-toggle="collapse" data-target="#Step_1">
                Показать файлы  <i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
            </button>
        </h5>
    </div>
    <div id="Step_1" class="collapse" aria-labelledby="headingOne" data-parent="#parser-content">
        <div class="card-body">
            <div class="parser-nav-bar">
                <div class="parser-nav-bar-container">
                    <a href="" class="btn btn-danger btn-sm">
                        <i class="fa fa-broom" aria-hidden="true"></i> Очистить</a>
                </div>
            </div>
            <div id="pagination-content">
                <table cellpadding="1" cellspacing="1" border="0" style="margin-bottom: 5px;" class="table-striped table-mine full-width box-shadow--2dp">
                    <thead>
                    <tr class="tr-table-header">
                        <th>
                            <input id="check_start" class="hidden-checkbox" type="checkbox"/>
                            <label for="check_start">
                                <div><i class="fa fa-check"></i></div>
                            </label>
                        </th>
                        <th>Имя файла</th>
                    </tr>
                    </thead>
                    <tbody id="table-pagination-content"></tbody>
                </table>
            </div>
                <div id="pagination">
                    <ul id="pagination-list" class="pagination"></ul>
                </div>
        </div>
    </div>
</div>





