<?php

namespace App\Listeners;

use App\Services\GenerateVerifyEmailMessage;
use App\Services\GenerateVerifyEmailUrl;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;

class CustomizeEmailVerification
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly GenerateVerifyEmailUrl $generateVerifyEmailUrl,
        private readonly GenerateVerifyEmailMessage $generateVerifyEmailMessage,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        VerifyEmail::createUrlUsing($this->generateVerifyEmailUrl);
        VerifyEmail::toMailUsing($this->generateVerifyEmailMessage);
    }
}
