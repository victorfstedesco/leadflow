<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'client_id',
        'meta_campaign_id',
        'name',
        'objective',
        'meta_status',
        'start_date',
        'stop_date',
        'daily_budget',
        'lifetime_budget',
        'insights',
        'last_synced_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'stop_date' => 'date',
        'daily_budget' => 'decimal:2',
        'lifetime_budget' => 'decimal:2',
        'insights' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function plannings(): BelongsToMany
    {
        return $this->belongsToMany(Planning::class, 'planning_campaign')
            ->withPivot(['local_status', 'notes'])
            ->withTimestamps();
    }

    public function getMetaStatusLabelAttribute(): string
    {
        return match ($this->meta_status) {
            'ACTIVE' => 'Ativa',
            'PAUSED' => 'Pausada',
            'DELETED' => 'Excluída',
            'ARCHIVED' => 'Arquivada',
            default => $this->meta_status ?? '—',
        };
    }

    public function getInsightAttribute(string $key, $default = null)
    {
        $insights = $this->insights ?? [];
        return $insights[$key] ?? $default;
    }
}
