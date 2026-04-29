<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanningGoal extends Model
{
    protected $fillable = [
        'planning_id',
        'title',
        'unit',
        'target_value',
        'current_value',
        'notes',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    public function planning(): BelongsTo
    {
        return $this->belongsTo(Planning::class);
    }

    public function getProgressPercentAttribute(): float
    {
        $target = (float) $this->target_value;
        if ($target <= 0) {
            return 0.0;
        }

        $current = (float) $this->current_value;
        $percent = ($current / $target) * 100;

        return max(0.0, min(100.0, $percent));
    }
}
