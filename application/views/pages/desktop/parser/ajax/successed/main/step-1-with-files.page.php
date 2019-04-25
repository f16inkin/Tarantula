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
    <div class="alert alert-warning" role="alert">
        В директории найдены файлы: <b><?=$content['files_count'];?> шт.</b>
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
                        <i class="fa fa-broom" aria-hidden="true"></i> Очистить</a>
                </div>
                <div class="parser-nav-bar-container">
                    <a href="" class="btn btn-primary btn-sm">
                        <i class="fa fa-chevron-circle-right" aria-hidden="true"></i> Обработать</a>
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
        <!---->
        <nav aria-label="...">
            <ul class="pagination">
                <li class="page-item disabled">
                    <span class="page-link">Предыдущая</span>
                </li>
                <?php for ($i = 1; $i< $content['pagination']+1; $i++) :?>
                    <li class="page-item"><a class="page-link" href="#"><?=$i;?></a></li>
                <?php endfor ;?>
                <li class="page-item"><a class="page-link" href="#">Следующая</a></li>
            </ul>
        </nav>
        <!---->
    </div>
</div>




