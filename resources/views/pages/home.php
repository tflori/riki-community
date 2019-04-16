<?php

/** @var callable $e */
/** @var Syna\View $v */

$v->extend('basics/card');
$v->provide('title', 'About this framework');

$v->start('markdown');
include __DIR__ . '/home.md';
$v->end();
?>
<?= $v->markdown($v->section('markdown')); ?>
