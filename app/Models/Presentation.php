<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presentation extends Model
{
    protected $fillable = [
        'client_id',
        'created_by',
        'token',
        'title',
        'campaign_ids',
        'since',
        'until',
        'insights',
        'expires_at',
        'active',
    ];

    protected $casts = [
        'campaign_ids' => 'array',
        'insights'     => 'array',
        'expires_at'   => 'datetime',
        'active'       => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isAvailable(): bool
    {
        return $this->active && !$this->isExpired();
    }

    public function statusLabel(): string
    {
        if (!$this->active) return 'Desativada';
        if ($this->isExpired()) return 'Expirada';
        return 'No ar';
    }
}
