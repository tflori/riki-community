<?php /** @var callable $e */ /** @var Syna\View $v */ ?>
<div class="card">
    <div class="card-content">
        <?php if ($v->section('title')) : ?>
            <span class="card-title"><?= $e($v->section('title')) ?></span>
        <?php endif; ?>
        <?= $v->section('content', $content ?? '') ?>
    </div>
</div>
