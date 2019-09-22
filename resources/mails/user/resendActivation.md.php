<?php

use Community\Model\User;
use Syna\View;

/** @var callable $e */
/** @var View $v */
/** @var User $user */
/** @var array $texts */
/** @var string $activationLink */
/** @var string $activationCode */

$v->provide('subject', 'Account activation at ' . $texts['domain']);

?>
## Welcome to ríki community!

<?= sprintf($texts['salutation'], $user->name ?? $user->displayName) ?>

To activate your account you need confirm your email address by clicking on the link below.

# [Activate your account](<?= $activationLink ?>)

You can also activate your account by entering the following code:

<h1 class="code"><?= $activationCode ?></h1>

If you have not registered at <?= $texts['domain'] ?> please ignore this eMail.

<?= $texts['closing'] ?>
