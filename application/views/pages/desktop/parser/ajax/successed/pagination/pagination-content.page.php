<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 27.04.2019
 * Time: 10:56
 */
?>
<table cellpadding="1" cellspacing="1" border="0" style="margin-bottom: 5px;" class="table-striped table-mine full-width box-shadow--2dp">
    <thead>
    <tr class="tr-table-header">
        <th>Имя файла</th>
        <th>Действие</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($content['page_data'] as $file) :?>
        <tr class="tr-table-content">
            <td><input type="text" class="transparent-inputs" value="<?=$file;?>" style="width: 100%" readonly></td>
            <td><a href="" class="btn btn-danger btn-sm">Удалить</a></td>
        </tr>
    <?php endforeach ;?>
    </tbody>
</table>
