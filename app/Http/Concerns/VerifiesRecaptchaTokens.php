<?php

namespace App\Http\Concerns;

use App\Application as app;
use stdClass;

trait VerifiesRecaptchaTokens
{
    protected function verifyRecaptchaToken(?string $token, string $ip = null): ?stdClass
    {
        $response = app::httpClient()->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => app::config()->recaptchaSecret,
                'response' => $token,
                'remoteip' => $ip,
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody());
        }

        return null;
    }
}
