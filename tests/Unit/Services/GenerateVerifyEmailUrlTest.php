<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\GenerateVerifyEmailUrl;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\Covers;

#[Covers(GenerateVerifyEmailUrl::class)]
class GenerateVerifyEmailUrlTest extends TestCase
{
    public function testInvokeGeneratesSignedUrl()
    {
        $user = new User();
        $user->email = "foo@bar.com";

        Config::swap(new class {
            public function get($key, $default = null)
            {
                return match ($key) {
                    'app.client_url' => 'http://client-app.com',
                    'auth.verification.expire' => 60,
                    default => $default
                };
            }
        });

        URL::swap(new class {
            public function temporarySignedRoute($name, $expiration, $parameters, $absolute = true)
            {
                return 'http://example.com/api/verify-email?signature=abc123';
            }
        });

        $generator = new GenerateVerifyEmailUrl();
        $url = ($generator)($user);

        $this->assertEquals(implode('', [
            'http://client-app.com/auth/verify-email/?',
            'verify-url=',
            urlencode('http://example.com/api/verify-email?signature=abc123')
        ]), $url);
    }
}
