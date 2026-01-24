<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait WithClientRoutes
{
    public function getVerifyEmailRoute(array $queryParams): string
    {
        return $this->getRoute(
            sprintf('/auth/verify-email/?%s', http_build_query($queryParams))
        );
    }

    private function getRoute(string $pathName): string
    {
        return Config::get('app.client_url') . '/' . ltrim($pathName, '/');
    }
}
