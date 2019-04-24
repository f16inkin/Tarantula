<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 11:25
 */
?>
<div class="alert alert-primary" role="alert">
    Шаг №1. Проверка директории и файлов
</div>
<div class="card">
    <div class="alert alert-danger" role="alert">
        Количество файлов в директории превышает допустимое. Найдено <b><?=$content['files_count'];?> шт.</b> из <b><?=$content['files_limit'];?> шт.</b> допустимых.
        Для продолжения работы парсера, необходимо удалить <b><u>лишние файлы.</u></b>
    </div>
    <div class="card-header" id="headingOne">
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
                        <i class="fa fa-broom" aria-hidden="true"></i> Очистить папку</a>
                </div>
                <div class="parser-nav-bar-container">
                    <a href="" class="btn btn-warning btn-sm" style="color: white">
                        <i class="fa fa-trash" aria-hidden="true"></i> Удалить</a>
                </div>
            </div>
            <table cellpadding="1" cellspacing="1" border="0" style="margin-bottom: 5px;" class="table-striped table-mine full-width box-shadow--2dp">
                <thead>
                <tr class="tr-table-header">
                    <th>Имя файла</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($content['files_array'] as $file) :?>
                    <tr class="tr-table-content">
                        <td><input type="text" class="transparent-inputs" value="<?=$file;?>" style="width: 100%" readonly></td>
                        <td><a href="" class="btn btn-danger btn-sm">Удалить</a></td>
                    </tr>
                <?php endforeach ;?>
                </tbody>
            </table>
        </div>
    </div>
</div>





