<?php

namespace App\Enum;

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\ExtrasTrait;
use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;

enum FilterType: string implements ReadableEnumInterface
{
    use ReadableEnumTrait;
    use ExtrasTrait;

    #[EnumCase(label: 'Category', extras: ['inputType' => 'text'])]
    case CATEGORY = 'category';

    #[EnumCase(label: 'News Source', extras: ['inputType' => 'text'])]
    case SOURCE = 'source';

    #[EnumCase(label: 'Keyword', extras: ['inputType' => 'text'])]
    case KEYWORD = 'keyword';

    #[EnumCase(label: 'Author', extras: ['inputType' => 'text'])]
    case AUTHOR = 'author';

    #[EnumCase(label: 'Date Published', extras: ['inputType' => 'date'])]
    case DATE_PUBLISHED = 'datePublished';

    #[EnumCase(label: 'Published Between', extras: ['inputType' => 'date-range'])]
    case PUBLISHED_BETWEEN = 'publishedBetween';

    public function getInputType(): string
    {
        return $this->getExtra('inputType');
    }
}
