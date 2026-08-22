<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusApproval extends Model
{
    protected $fillable = [
        'asset_id', 'requested_by', 'old_status', 'new_status',
        'reason', 'status', 'reviewed_by', 'review_notes', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}