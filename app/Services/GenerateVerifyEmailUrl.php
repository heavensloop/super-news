<?php

namespace App\Services;

use App\Traits\WithClientRoutes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class GenerateVerifyEmailUrl
{
    use WithClientRoutes;

    public function __invoke($notifiable): string
    {
        $verifyUrl = URL::temporarySignedRoute(
            'auth.email.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return $this->getVerifyEmailRoute([
            'verify-url' => $verifyUrl,
        ]);
    }
}
