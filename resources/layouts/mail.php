<?php /** @var Syna\View $v */ ?>
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?= $v->section('subject') ?></title>
    </head>
    <body>
        <?= $v->section('content') ?>
    </body>
</html>
