<?php

/** @var callable $e */
/** @var Syna\View $v */

$v->extend('basics/card');
$v->provide('title', 'Lorem Ipsum');

$v->start('markdown');
include __DIR__ . '/home.md';
$v->end();
echo $v->markdown($v->section('markdown'));
