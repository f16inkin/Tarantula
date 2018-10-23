<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 21.08.2018
 * Time: 16:40
 */
?>
<div class="upload-panel">
    <form enctype="multipart/form-data" id="xmlform" name="xmlform" method="post">
        <label for="xml_file">Загрузить XML - отчет</label>
        <div class="input-group small under-all">
            <span class="input-group-addon"><i class="fa-mine fa-file-image-o fa-red" aria-hidden="true"></i></span>
            <input class="form-control" type="file" id="xml_file" name="xml_file">
            <input type="text" name="image_kind" value="logo" hidden>
        </div>
        <select name="subdivision_id">
            <?php foreach ($content['subdivisions'] as $id => $name) :?>
            <option value="<?=$id;?>"><?=$name;?></option>

            <?php endforeach; ?>
        </select>
        <a href="#" class="btn btn-primary btn-sm" onclick="upload('xmlform')">Загрузить XML</a>
        <a href="#" class="btn btn-primary btn-sm" onclick="getParsedData()">Получить распарсенные данные</a>
        <a href="#" class="btn btn-primary btn-sm" onclick="getDataByDate()">Получить данные из БД</a>
        <a href="#" class="btn btn-primary btn-sm" onclick="getDataByXml()">Получить данные из Xml файлов</a>
    </form>
</div>
<div id="response"></div>
<script src="/views/layouts/template/js/tarantula.js"></script>
