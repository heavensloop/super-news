<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property array $settings
 * @property bool $is_default
 * @property bool $is_subscribed
 */
class UserFilter extends Model
{
    /** @use HasFactory<\Database\Factories\UserFilterFactory> */
    use HasFactory;

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean',
        'is_subscribed' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'is_default',
        'is_subscribed',
    ];
}
