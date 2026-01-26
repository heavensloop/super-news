<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'settings' => $this->settings,
            'is_default' => $this->is_default,
            'is_subscribed' => $this->is_subscribed,
            'created_at' => $this->created_at,
        ];
    }
}
