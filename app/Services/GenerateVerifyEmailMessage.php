<?php

namespace App\Services;

use Illuminate\Notifications\Messages\MailMessage;

class GenerateVerifyEmailMessage
{
    public function __invoke($notifiable, string $url): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $url)
            ->line('If you did not create an account, no further action is required.');
    }
}
