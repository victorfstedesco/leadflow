<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $fillable = [
        'client_id', 'funnel_stage_id', 'name', 'email', 'phone', 'source', 'notes', 'entered_at',
    ];

    protected $casts = [
        'entered_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(FunnelStage::class, 'funnel_stage_id');
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class)->latest();
    }

    public function histories(): HasMany
    {
        return $this->hasMany(LeadHistory::class)->latest('moved_at');
    }
}
