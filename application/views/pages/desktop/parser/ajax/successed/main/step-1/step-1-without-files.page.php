<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 12:35
 */
?>
<div class="alert alert-danger" role="alert">В директории отсутствуют файлы</div>

<form id="upload-reports-form" method="POST" enctype="multipart/form-data">
    <input id="upload-reports" type="file" name="reports[]" multiple="multiple"/>
    <div class="parser-nav-bar">
        <div class="parser-nav-bar-container">
            <button class="btn btn-success btn-sm" onclick="reportsUpload(); return false;">
                <i class="fa fa-upload" aria-hidden="true"></i> Загрузить</button>
        </div>
    </div>
</form>