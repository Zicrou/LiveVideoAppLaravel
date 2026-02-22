<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Post;
use App\Http\Resources\UserResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'post_id' => $this->resource->id,
            'post_type' => $this->resource->post_type,
            'owner' => $this->resource->owner,
            'user' => new UserResource($this->whenLoaded('owner')),
            'video' => new VideoResource($this->whenLoaded('video')),
            'like' => new LikeResource($this->whenLoaded('like')),
        ];
    }
}
