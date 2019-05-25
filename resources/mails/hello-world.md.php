<?php /** @var callable $e */ /** @var Syna\View $v */ /** @var string $name */ ?>
<?php $v->provide('subject', 'A test email from ríki community') ?>
## This is a test email

It's content is *markdown*. Parsed with **parsedown**.

But it is also php so "Hello <?= $name ?>!"

# [Visit ríki community](https://riki.w00tserver.org)

The next steps are:
- create email templates for sending activation codes
- create an activation code when a user get created
- send email with activation codes
- create routes for activating users by activation token and activation code
