<?php

namespace Tests\Unit\Listeners;

use App\Listeners\CustomizeEmailVerification;
use App\Models\User;
use App\Services\GenerateVerifyEmailMessage;
use App\Services\GenerateVerifyEmailUrl;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CustomizeEmailVerification::class)]
class CustomizeEmailVerificationTest extends TestCase
{
    public function test_handle(): void
    {
        $user = new User();
        $customUrl = 'http://example.com/verify-email-link';

        $generateVerifyEmailUrl = $this->createMock(GenerateVerifyEmailUrl::class);
        $generateVerifyEmailUrl->method('__invoke')
            ->with($user)
            ->willReturn($customUrl);

        $customMailMessage = new MailMessage();

        $generateVerifyEmailMessage = $this->createMock(GenerateVerifyEmailMessage::class);
        $generateVerifyEmailMessage->method('__invoke')
            ->with($user, $customUrl)
            ->willReturn($customMailMessage);

        $listener = new CustomizeEmailVerification($generateVerifyEmailUrl, $generateVerifyEmailMessage);
        $event = new Registered($user);

        $listener->handle($event);

        $this->assertEquals($customUrl, (VerifyEmail::$createUrlCallback)($user));
        $this->assertEquals($customMailMessage, (VerifyEmail::$toMailCallback)($user, $customUrl));
    }
}
