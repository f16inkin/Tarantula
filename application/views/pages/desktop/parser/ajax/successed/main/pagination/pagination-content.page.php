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
        <th>
            <input id="check_start" class="hidden-checkbox" type="checkbox"/>
            <label for="check_start">
                <div><i class="fa fa-check"></i></div>
            </label>
        </th>
        <th>Имя файла</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($content['page_data'] as $key => $value) :?>
        <tr id="table_line_<?=$key;?>" class="tr-table-content">
            <td>
                <input id="check_<?=$key;?>" class="hidden-checkbox" type="checkbox" value="<?=$value?>"/>
                <label for="check_<?=$key;?>">
                    <div><i class="fa fa-check"></i></div>
                </label>
            </td>
            <td><?=$value;?></td>
        </tr>
    <?php endforeach ;?>
    </tbody>
</table>