<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkedInCommentLead extends Model
{
    protected $table = 'linkedin_comment_leads';

    protected $fillable = [
        'linkedin_post_id',
        'commenter_urn',
        'commenter_name',
        'comment_id',
        'comment_text',
        'keyword_matched',
        'dm_status',
        'dm_sent_at',
        'followup_status',
        'followup_sent_at',
        'error_message',
    ];

    protected $casts = [
        'dm_sent_at'       => 'datetime',
        'followup_sent_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(LinkedInPost::class, 'linkedin_post_id');
    }

    public function scopePendingDM($query)
    {
        return $query->where('dm_status', 'pending');
    }

    public function scopePendingFollowUp($query)
    {
        return $query->where('dm_status', 'sent')
                     ->where('followup_status', 'pending')
                     ->where('dm_sent_at', '<=', now()->subHours(
                         config('linkedin.followup_delay_hours', 24)
                     ));
    }
}
