<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ArticleResource extends JsonResource
{
    public static $wrap = false;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'category' => $this->category,
            'source' => $this->source,
            'url' => $this->url,
            'image_url' => $this->image_url,
            'author' => $this->author,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'slug' => Str::slug($this->title),
            'formatted_date' => $this->published_at?->format('F j, Y'),
        ];
    }
}
