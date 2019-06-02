<?php

namespace App\Http\Controller;

use App\Application as a;
use Carbon\Carbon;
use Community\Model\Token\ActivationCode;
use Community\Model\Token\ActivationToken;
use Community\Model\User;
use Tal\ServerResponse;
use Verja\Error;

class UserController extends AbstractController
{
    public function register()
    {
        $em = a::entityManager();

        if (!$this->validate([
            'email' => ['required', 'notEmpty', 'emailAddress', function ($value) use ($em) {
                return $em->fetch(User::class)->where('email', $value)->count() === 0 ? true :
                    new Error('EMAIL_TAKEN', $value, 'Email address already taken');
            }],
            'password' => ['required', 'notEmpty', 'passwordStrength:50', 'equals:passwordConfirmation'],
            'displayName' => ['required', 'notEmpty', 'pregMatch:/^[\w @._-]+$/', function ($value) use ($em) {
                return $em->fetch(User::class)->where('displayName', $value)->count() === 0 ? true :
                    new Error('DISPLAY_NAME_TAKEN', $value, 'Display name already taken');
            }],
            'name' => ['pregMatch:/^[\p{L}\p{N} .-]+$/u']
        ], 'json', $userData, $errors)) {
            return $this->error(400, 'Bad Request', 'Invalid userdata', $errors);
        }

        $user = new User();
        $user->fill($userData);
        $user->save();

        return new ServerResponse(200, ['Content-Type' => 'application/json'], json_encode($user));
    }
}
