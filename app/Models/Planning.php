<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Planning extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'period_start',
        'period_end',
        'status',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(PlanningGoal::class);
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'planning_campaign')
            ->withPivot(['local_status', 'notes'])
            ->withTimestamps();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'ativo' => 'Ativo',
            'pausado' => 'Pausado',
            'concluido' => 'Concluído',
            'arquivado' => 'Arquivado',
            default => $this->status ?? '—',
        };
    }
}
