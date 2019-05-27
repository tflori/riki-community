<?php

use Community\Model\User;
use Syna\View;

/** @var callable $e */
/** @var View $v */
/** @var User $user */
/** @var string $domain */
/** @var string $activationLink */
/** @var string $activationCode */

$v->provide('subject', 'Your registration at ríki community');

?>
## Welcome to ríki community!

Hello <?= $e($user->name ?: $user->displayName) ?>,

thanks for your registration at <?= $domain ?>. To activate your account you need confirm your email address by clicking
on the link below.

# [Activate your account](<?= $activationLink ?>)

You can also activate your account by entering the following code:

<h1 class="code"><?= $activationCode ?></h1>

If you have not registered at <?= $domain ?> please ignore this eMail.

Best regards
Your ríki community
