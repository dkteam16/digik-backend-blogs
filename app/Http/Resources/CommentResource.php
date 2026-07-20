<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'post_id'     => $this->post_id,
            'author_name' => $this->author_name,
            'content'     => $this->content,
            'is_approved' => $this->is_approved,
            'parent_id'   => $this->parent_id,
            'replies'     => CommentResource::collection($this->whenLoaded('replies')),
            'created_at'  => $this->created_at->toISOString(),
        ];
    }
}
