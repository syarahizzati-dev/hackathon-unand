<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumPost extends Model
{
    protected $fillable = [
        'user_id',
        'konten',
        'label',
        'risk_level',
        'is_hidden',
    ];

    protected function casts(): array
    {
        return [
            'label'     => 'integer',
            'is_hidden' => 'boolean',
        ];
    }

    // ─── Relasi ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'post_id')->whereNull('parent_id')->oldest();
    }

    public function allReplies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'post_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ForumLike::class, 'post_id');
    }
}
