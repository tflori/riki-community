<?php

namespace App\Http\Controller\Concerns;

use App\Application as app;
use Tal\ClientRequest;
use function GuzzleHttp\Psr7\stream_for;

trait VerifiesRecaptchaTokens
{
    protected function verifyRecaptchaToken(?string $token, string $ip = null): ?\stdClass
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
