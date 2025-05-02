<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PornstarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->external_id,
            'name' => $this->name,
            'license' => $this->license,
            'wl_status' => (bool)$this->wl_status,
            'link' => $this->link,
            'attributes' => $this->attributes,
            'aliases' => $this->aliases,
            'thumbnail_url' => $this->thumbnail_url,
            'stats' => [
                'views' => $this->attributes['stats']['views'] ?? 0,
                'rank' => $this->attributes['stats']['rank'] ?? null,
            ],
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            '_links' => [
                'self' => route('api.pornstars.show', $this->external_id)
            ]
        ];
    }
}
