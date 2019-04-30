<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 30.04.2019
 * Time: 11:14
 */
?>
<ul class="pagination">
    <?php for ($i = 1; $i< $content['pagination']+1; $i++) :?>
        <li class="page-item"><a class="page-link" onclick="showPaginationPageData(<?=$i;?>, 1); return false;"><?=$i;?></a></li>
    <?php endfor ;?>
</ul>
