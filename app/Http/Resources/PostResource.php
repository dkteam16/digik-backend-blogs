<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'slug'             => $this->slug,
            'excerpt'          => $this->excerpt,
            'content'          => $this->content,
            'featured_image'   => $this->featured_image_url,
            'status'           => $this->status,
            'is_featured'      => $this->is_featured,
            'allow_comments'   => $this->allow_comments,
            'views_count'      => $this->views_count,
            'reading_time'     => $this->reading_time . ' min read',
            'published_at'     => $this->published_at?->toISOString(),
            'created_at'       => $this->created_at->toISOString(),
            'updated_at'       => $this->updated_at->toISOString(),
            'author'           => new UserResource($this->whenLoaded('author')),
            'category'         => new CategoryResource($this->whenLoaded('category')),
            'tags'             => TagResource::collection($this->whenLoaded('tags')),
            'comments_count'   => $this->when(
                $this->relationLoaded('approvedComments'),
                fn () => $this->approvedComments->count()
            ),
            'seo' => [
                'meta_title'       => $this->meta_title ?? $this->title,
                'meta_description' => $this->meta_description ?? $this->excerpt,
                'meta_keywords'    => $this->meta_keywords,
            ],
        ];
    }
}
