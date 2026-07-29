<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HiringPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'description'           => $this->description,
            'qualification'         => $this->qualification,
            'experience'            => $this->experience,
            'department'            => $this->department,
            'location'              => $this->location,
            'work_type'             => $this->work_type,
            'employment_type'       => $this->employment_type,
            'apply_url'             => $this->apply_url,
            'status'                => $this->status,
            'is_featured'           => $this->is_featured,
            'views_count'           => $this->views_count,
            'application_deadline'  => $this->application_deadline?->toDateString(),
            'is_expired'            => $this->isExpired(),
            'published_at'          => $this->published_at?->toISOString(),
            'created_at'            => $this->created_at->toISOString(),
            'updated_at'            => $this->updated_at->toISOString(),
            'author'                => new UserResource($this->whenLoaded('author')),
            'seo' => [
                'meta_title'       => $this->meta_title ?? $this->title,
                'meta_description' => $this->meta_description ?? $this->description,
                'meta_keywords'    => $this->meta_keywords,
            ],
        ];
    }
}
