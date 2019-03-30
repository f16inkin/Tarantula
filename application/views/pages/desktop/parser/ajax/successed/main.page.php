<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 19.03.2019
 * Time: 11:30
 */
?>
AJAX Page
Главная страница с возможно инструкцией
Тут же будет возможность загружать на сервер сами XML файлы
<div>
    <button type="button" class="btn btn-success btn-sm">
        Смотреть <span class="badge badge-light"><?=$content['files']['correct'];?></span>
    </button>
</div>
<hr>
<div>
    <button type="button" class="btn btn-danger btn-sm">
        Смотреть <span class="badge badge-light"><?=$content['files']['incorrect'];?></span>
    </button>
</div>
