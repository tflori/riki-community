<?php

namespace App\Http\Controller;

use App\Application as a;
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

        list($valid, $userData, $errors) = $this->request->validate([
            'email' => ['required', 'notEmpty', 'emailAddress', function ($value) use ($em) {
                if (!$value) {
                    return true;
                }
                return $em->fetch(User::class)->where('email', $value)->count() === 0 ? true :
                    new Error('EMAIL_TAKEN', $value, 'Email address already taken');
            }],
            'password' => ['required', 'notEmpty', 'passwordStrength:50', 'equals:passwordConfirmation'],
            'displayName' => ['required', 'notEmpty', 'pregMatch:/^[\w @._-]+$/', function ($value) use ($em) {
                if (!$value) {
                    return true;
                }
                return $em->fetch(User::class)->where('displayName', $value)->count() === 0 ? true :
                    new Error('DISPLAY_NAME_TAKEN', $value, 'Display name already taken');
            }],
            'name' => ['pregMatch:/^[\p{L}\p{N} .-]+$/u']
        ], 'json', [
            'password.NOT_EQUAL' => 'Passwords don\'t match',
            'displayName.NO_MATCH' => 'Only word characters, spaces, dots, dashes and at signs are allowed',
            'name.NO_MATCH' => 'Only letters, numbers, spaces, dots and dashes are allowed',
        ]);
        if (!$valid) {
            return $this->error(400, 'Bad Request', 'Invalid user data', $errors);
        }

        $user = new User();
        $user->fill($userData);
        $user->save();

        $activationCode = ActivationCode::newToken($user, '1d')->save();
        $activationToken = ActivationToken::newToken($user, '7d')->save();

        a::mailer()->send(a::mail('user/registration', [
            'user' => $user,
            'activationLink' => a::environment()->url('user/activate', $activationToken->token),
            'activationCode' => $activationCode->token,
        ])->addTo($user->email));

        $this->app->session->set('user', $user);
        return $this->json($user);
    }
}
