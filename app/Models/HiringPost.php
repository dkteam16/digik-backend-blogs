<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HiringPost extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_DRAFT     = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_CLOSED    = 'closed';
    const STATUS_ARCHIVED  = 'archived';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'qualification',
        'experience',
        'department',
        'location',
        'work_type',
        'employment_type',
        'apply_url',
        'status',
        'author_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views_count',
        'is_featured',
        'application_deadline',
        'published_at',
    ];

    protected $casts = [
        'is_featured'           => 'boolean',
        'views_count'           => 'integer',
        'published_at'          => 'datetime',
        'application_deadline'  => 'date',
    ];

    // ── Scopes ──────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
                     ->where('published_at', '<=', now());
    }

    public function scopeOpen($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('application_deadline')
              ->orWhere('application_deadline', '>=', now()->toDateString());
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    // ── Relationships ────────────────────────────────────────
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // ── Helpers ──────────────────────────────────────────────
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isExpired(): bool
    {
        return $this->application_deadline !== null
            && $this->application_deadline->isPast();
    }
}
