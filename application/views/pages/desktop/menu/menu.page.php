<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 30.06.2018
 * Time: 13:39
 */
?>
<nav class="main-menu">
    <div class="area">
        <!--<div class="settings"></div>-->
        <ul>
        <?php if(isset($content))foreach ($content['menu'] as $menuItem):?>
            <li>
                <a href="<?php echo $menuItem['link'] ;?>">
                    <i class="fa-for-menu <?php echo $menuItem['style'] ;?>"></i>
                    <span class="nav-text"><?php echo $menuItem['title'];?></span>
                </a>
            </li>
        <?php endforeach;?>
        </ul>
    </div>
</nav>
