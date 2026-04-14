<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = ['user_id', 'name', 'channels', 'notes'];

    protected $casts = [
        'channels' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(FunnelStage::class)->orderBy('position');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
