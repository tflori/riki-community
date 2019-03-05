<?php /** @var callable $e */ /** @var Syna\View $v */ ?>
<div class="card">
    <article class="card-content">
        <?php if ($v->section('title')) : ?>
        <header>
            <span class="card-title"><?= $e($v->section('title')) ?></span>
        </header>
        <?php endif; ?>
        <?= $v->section('content', $content ?? '') ?>
    </article>
</div>
