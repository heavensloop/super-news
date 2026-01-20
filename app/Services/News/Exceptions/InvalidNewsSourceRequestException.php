<?php

namespace App\Services\News\Exceptions;

use App\Enum\NewsSource;

class InvalidNewsSourceRequestException extends \RuntimeException
{
    public function __construct(
        protected readonly string $message = "Invalid request to news source API.",
        protected readonly NewsSource $resourceType
    ) {
        parent::__construct(sprintf("%s - Resource Type: %s", $this->message, $this->resourceType::class), 500);
    }

    public function getResourceType(): NewsSource
    {
        return $this->resourceType;
    }
}
