<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'niche',
        'channels',
        'notes',
        'meta_user_id',
        'meta_access_token',
        'meta_ad_account_id',
        'meta_token_expires_at',
        'meta_last_synced_at',
    ];

    protected $casts = [
        'channels' => 'array',
        'meta_access_token' => 'encrypted',
        'meta_token_expires_at' => 'datetime',
        'meta_last_synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class);
    }

    public function isMetaConnected(): bool
    {
        return ! empty($this->meta_access_token) && ! empty($this->meta_ad_account_id);
    }
}
