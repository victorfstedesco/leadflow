<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanningGoal extends Model
{
    const CATEGORIES = [
        'reach'       => ['label' => 'Alcance',     'unit' => 'pessoas', 'icon' => 'visibility',    'field' => 'reach'],
        'impressions' => ['label' => 'Impressões',   'unit' => 'vezes',   'icon' => 'bar_chart',     'field' => 'impressions'],
        'clicks'      => ['label' => 'Cliques',      'unit' => 'cliques', 'icon' => 'ads_click',     'field' => 'clicks'],
        'ctr'         => ['label' => 'CTR',          'unit' => '%',       'icon' => 'percent',       'field' => 'ctr'],
        'cpc'         => ['label' => 'CPC',          'unit' => 'R$',      'icon' => 'payments',      'field' => 'cpc'],
        'spend'       => ['label' => 'Investimento', 'unit' => 'R$',      'icon' => 'attach_money',  'field' => 'spend'],
    ];

    protected $fillable = [
        'planning_id',
        'category',
        'title',
        'unit',
        'target_value',
        'current_value',
        'notes',
    ];

    protected $casts = [
        'target_value'  => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    public function planning(): BelongsTo
    {
        return $this->belongsTo(Planning::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category]['label'] ?? ($this->title ?? $this->category ?? '—');
    }

    public function getCategoryUnitAttribute(): string
    {
        return self::CATEGORIES[$this->category]['unit'] ?? ($this->unit ?? '');
    }

    public function getCategoryIconAttribute(): string
    {
        return self::CATEGORIES[$this->category]['icon'] ?? 'flag';
    }

    public function getProgressPercentAttribute(): float
    {
        $target = (float) $this->target_value;
        if ($target <= 0) {
            return 0.0;
        }

        return max(0.0, min(100.0, ((float) $this->current_value / $target) * 100));
    }
}
