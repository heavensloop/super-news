<?php

namespace App\Models;

use App\Enum\ContentStatus;
use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
    @property DateTimeInterface $published_at
    @property string $title
    @property string $description
    @property string $content
    @property string $url
    @property ?string $image_url
    @property ?string $author
    @property NewsCategory $category
    @property NewsSource $source
    @property ContentStatus $content_status
*/

class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;

    // Save category as enum
    protected $casts = [
        'category' => NewsCategory::class,
        'source' => NewsSource::class,
        'published_at' => 'datetime',
        'content_status' => ContentStatus::class,
    ];
}
