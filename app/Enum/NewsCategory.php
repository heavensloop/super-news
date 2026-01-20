<?php

namespace App\Enum;

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;

enum NewsCategory: string implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    #[EnumCase('World News')]
    case WORLD_NEWS = 'world-news';

    #[EnumCase('Politics & Government')]
    case POLITICS = 'politics';

    #[EnumCase('Business & Economy')]
    case BUSINESS = 'business';

    #[EnumCase('Science & Technology')]
    case SCIENCE_TECH = 'science-tech';

    #[EnumCase('Health')]
    case HEALTH = 'health';

    #[EnumCase('Sports')]
    case SPORTS = 'sports';

    #[EnumCase('Entertainment')]
    case ENTERTAINMENT = 'entertainment';

    #[EnumCase('Crime & Justice')]
    case CRIME_JUSTICE = 'crime-justice';

    #[EnumCase('Lifestyle')]
    case LIFESTYLE = 'lifestyle';
}
