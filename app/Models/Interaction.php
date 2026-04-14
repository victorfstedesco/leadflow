<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    protected $fillable = ['lead_id', 'type', 'description'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
