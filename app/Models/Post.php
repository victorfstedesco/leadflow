<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $fillable = [
        'client_id',
        'title',
        'copy',
        'content_type',
        'objective',
        'campaign_id',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getContentTypeLabelAttribute(): string
    {
        return match ($this->content_type) {
            'imagem' => 'Imagem',
            'video' => 'Vídeo',
            'carrossel' => 'Carrossel',
            'story' => 'Story',
            'reels' => 'Reels',
            default => $this->content_type,
        };
    }

    public function getContentTypeIconAttribute(): string
    {
        return match ($this->content_type) {
            'video' => 'videocam',
            'reels' => 'smart_display',
            'carrossel' => 'view_carousel',
            'story' => 'amp_stories',
            default => 'image',
        };
    }

    public function getObjectiveLabelAttribute(): string
    {
        return match ($this->objective) {
            'engajamento' => 'Engajamento',
            'conversao' => 'Conversão',
            'branding' => 'Branding',
            'educacao' => 'Educação',
            default => $this->objective ?? '—',
        };
    }
}
