<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 19.08.2018
 * Time: 15:18
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?=$this->_title?></title>
    <link rel="stylesheet" href="/core/views/layouts/desktop/errors/css/style.css">
</head>
<body>
<div class="error">
    <h1>Oooppps. Something goes wrong</h1>
    <?php include $this->_pages.$page.'.php'; ?>
</div>
</body>
</html>
