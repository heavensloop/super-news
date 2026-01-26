<?php

namespace App\Enum;

enum ContentStatus: string
{
    case PENDING = 'pending';
    case POPULATED = 'populated';
    case FAILED = 'failed';
}
