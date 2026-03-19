<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinkedInPost extends Model
{
    protected $table = 'linkedin_posts';

    protected $fillable = [
        'rotation_index',
        'post_type',
        'angle',
        'generated_text',
        'linkedin_post_id',
        'status',
        'keyword',
        'resource_name',
        'error_message',
        'scheduled_for',
        'published_at',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'published_at'  => 'datetime',
    ];

    public function commentLeads(): HasMany
    {
        return $this->hasMany(LinkedInCommentLead::class, 'linkedin_post_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeLeadMagnets($query)
    {
        return $query->where('post_type', 'lead_magnet');
    }
}
