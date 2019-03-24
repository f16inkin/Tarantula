<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 23.03.2019
 * Time: 19:08
 */
?>
<select id="modal-subdivisions" style="width: 250px;">
    <?php foreach ($content as $singleSubdivision) :?>
        <option value="<?=$singleSubdivision['id'];?>"><?=$singleSubdivision['name'];?></option>
    <?php endforeach; ?>
</select>
