<?php

namespace App\Enum;

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;

enum NewsSource: string implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    #[EnumCase('News API')]
    case NEWS_API = 'newsapi';

    #[EnumCase('The Guardian')]
    case THE_GUARDIAN = 'theguardian';
}
