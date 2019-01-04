<?php /** @var callable $e */ /** @var Syna\View $v */ ?>
<?php $v->extend('basics/card'); ?>
<?php $v->provide('title', 'Lorem Ipsum') ?>

<?= $v->markdown(file_get_contents(__DIR__ . '/home.md'));
