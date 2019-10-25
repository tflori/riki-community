<?php

namespace App\Http\Controller\Concerns;

use App\Application;
use Tal\ClientRequest;
use function GuzzleHttp\Psr7\stream_for;

trait VerifiesRecaptchaTokens
{
    protected function verifyRecaptchaToken(string $token, string $ip = null): ?\stdClass
    {
        $response = Application::httpClient()->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => Application::config()->recaptchaSecret,
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
