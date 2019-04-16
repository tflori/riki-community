<?php /** @var callable $e */ /** @var Syna\View $v */ ?>
<div class="card">
  <article class="card-content">
      <?php if ($v->section('title')) : ?>
        <header class="card-title"><?= $e($v->section('title')) ?></header>
      <?php endif; ?>
      <?= $v->section('content', $content ?? '') ?>
  </article>
</div>
