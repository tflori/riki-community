<?php

namespace App\Http\Controller;

use App\Application as a;
use App\Http\Controller\Concerns\VerifiesRecaptchaTokens;
use App\Model\Request;
use Carbon\Carbon;
use Community\Model\Token\ActivationCode;
use Community\Model\Token\ActivationToken;
use Community\Model\User;
use Tal\ServerResponse;
use Verja\Error;

class UserController extends AbstractController
{
    use VerifiesRecaptchaTokens;

    /**
     * Register a new User
     *
     * Expects a json encoded body with the following data:
     * {
     *     "email": "emailAddress",
     *     "password": "passwordStrength:50",
     *     "passwordConfirmation": "passwordStrength:50",
     *     "displayName": "pregMatch:/^[\w @._-]+$/",
     *     "name": null|"pregMatch:/^[\p{L}\p{N} .-]+$/u"
     * }
     *
     * Returns a json encoded user
     *
     * @route POST /user
     * @param Request $request
     * @return ServerResponse
     */
    public function register(Request $request): ServerResponse
    {
        $em = $this->app->entityManager;

//        $recaptchaResponse = $this->verifyRecaptchaToken($request->get('recaptchaToken'), $request->getIp());

        list($valid, $userData, $errors) = $request->validate([
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
            }, 'strLen:1:20'],
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

        $this->app->mailer->send(a::mail('user/registration', [
            'user' => $user,
            'activationLink' => $this->app->environment->url('user/activate', $activationToken->token),
            'activationCode' => $activationCode->token,
        ])->addTo($user->email));

        $this->app->session->set('user', $user);
        return $this->json($user);
    }

    /**
     * Activate an User by activation code
     *
     * Requires authentication and verified CSRF token.
     *
     * Expects a json encoded body with the following data:
     * {
     *     "name": "string"
     * }
     *
     * Returns a json encoded user
     *
     * @route POST /user
     * @param Request $request
     * @return ServerResponse
     */
    public function activate(Request $request): ServerResponse
    {
        if (!$user = $this->app->session->get('user')) {
            return $this->error(401, 'Unauthorized', 'This service requires authorization');
        }
        /** @var User $user */

        if (!$request->getAttribute('csrfTokenVerified')) {
            return ($this->error(400, 'Bad Request', 'Invalid request token'));
        }

        list(,$data) = $request->validate(['token'], 'json');
        $activationCode = $this->app->entityManager->fetch(ActivationCode::class)
            ->where('user_id', $user->id)
            ->where('valid_until', '>', Carbon::now())
            ->where('token', $data['token'])
            ->one();
        if (!$activationCode) {
            return $this->error(400, 'Bad Request', 'Invalid activation code');
        }

        if ($user->accountStatus !== User::PENDING) {
            return $this->error(400, 'Bad Request', 'Account disabled');
        }

        $user->activate();
        $user->save();

        return $this->json($user);
    }

    public function activateByToken(Request $request, $token): ServerResponse
    {
        /** @var ActivationToken $activationToken */
        $activationToken = $this->app->entityManager->fetch(ActivationToken::class)
            ->where('valid_until', '>', Carbon::now())
            ->where('token', $token)
            ->one();
        if (!$activationToken) {
            return $this->error(400, 'Bad Request', 'Invalid activation token');
        }

        $user = $activationToken->user;
        if ($user->accountStatus !== User::PENDING) {
            return $this->error(400, 'Bad Request', 'Account disabled');
        }

        $user->activate();

        return $this->redirect('/');
    }

    public function resendActivation(Request $request)
    {
        if (!$user = $this->app->session->get('user')) {
            return $this->error(401, 'Unauthorized', 'This service requires authorization');
        }
        /** @var User $user */

        if (!$request->getAttribute('csrfTokenVerified')) {
            return ($this->error(400, 'Bad Request', 'Invalid request token'));
        }

        if ($user->accountStatus !== User::PENDING) {
            return $this->error(400, 'Bad Request', 'Account disabled');
        }

        $activationCode = ActivationCode::newToken($user, '1d')->save();
        $activationToken = ActivationToken::newToken($user, '7d')->save();

        $this->app->mailer->send(a::mail('user/resendActivation', [
            'user' => $user,
            'activationLink' => $this->app->environment->url('user/activate', $activationToken->token),
            'activationCode' => $activationCode->token,
        ])->addTo($user->email));

        return $this->json('OK');
    }
}
