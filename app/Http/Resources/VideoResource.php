<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Video;

/** 
 * @video Video $resource
*/
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
            'post_type' => $this->resource->post->post_type,
            // 'like' => $this->resource->likes,
            // 'like_count' => $this->resource->likes->count(),
            'likes' => LikeResource::collection(($this->whenLoaded('likes')))
        ];
    }
}
