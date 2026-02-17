<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'video_url' => $this->resource->video_url,
            'caption' => $this->resource->caption,
            'owner_id' => $this->resource->owner_id,
            'post_id' => $this->resource->post_id,
        ];
    }
}
