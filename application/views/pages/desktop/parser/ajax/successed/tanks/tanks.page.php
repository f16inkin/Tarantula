<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 21.03.2019
 * Time: 18:44
 */
?>
<div class="parser-nav-bar">
    <div class="parser-nav-bar-container">
        <select id="subdivisions" style="width: 250px;">
            <option value="0">Выберите подраздедление...</option>
            <?php foreach ($content as $singleSubdivision) :?>
                <option value="<?=$singleSubdivision['id'];?>"><?=$singleSubdivision['name'];?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="parser-nav-bar-container">
        <a href="" onclick="showTanksData();return false;" class="btn btn-success btn-sm">
            <i class="fa fa-upload" aria-hidden="true"></i> Загрузить XML</a>
    </div>
    <div class="parser-nav-bar-container">
        <a href="" onclick="cleanTanksPage();return false;" class="btn btn-danger btn-sm">
            <i class="fa fa-broom" aria-hidden="true"></i> Очистить</a>
    </div>
    <div id="hidden-button" class="parser-nav-bar-container" hidden>
        <a href="" id="handbook-button" class="btn btn-primary btn-sm" onclick="">
            <i class="fa fa-plus-circle" aria-hidden="true"></i> Добавить в БД</a>
    </div>
</div>
<div id="tanks-content"></div>
<!--Modal Window-->
<div class="modal fade" id="tanksModalWindow" tabindex="-1" role="dialog" aria-labelledby="tanksModalWindowTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Выберите подразделение</h5>
                <a href="" class="close" data-dismiss="modal">
                    <i class="fa fa-times-circle" aria-hidden="true"></i></a>
            </div>
            <div id="modal-content" class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <a id="modalUploadXmlButton" href="" onclick="return false;" class="btn btn-success btn-sm">
                    <i class="fa fa-upload" aria-hidden="true"></i> Загрузить XML</a>
            </div>
        </div>
    </div>
</div>
<!---->