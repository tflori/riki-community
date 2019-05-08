<?php

namespace App\Http\Controller;

use Community\Model\User;
use Tal\ServerResponse;

class UserController extends AbstractController
{
    public function createUser()
    {
        if (!$this->validate([
            'email' => ['required', 'notEmpty', 'emailAddress'],
            'password' => ['required', 'notEmpty', 'passwordStrength:50'],
            'passwordConfirmation' => ['required', 'notEmpty', 'equals:password'],
            'displayName' => ['required', 'notEmpty', 'pregMatch:/^[\w @._-]+$/'],
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
