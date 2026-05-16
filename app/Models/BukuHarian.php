<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BukuHarian extends Model
{
    protected $table = 'buku_harian';

    protected $fillable = [
        'user_id',
        'isi',
        'ai_reply',
        'ai_saran',
        'label',
        'risk_level',
        'confidence',
        'analysis_metadata',
        'is_analyzed',
    ];

    protected function casts(): array
    {
        return [
            'ai_saran'    => 'array',
            'label'       => 'integer',
            'confidence'  => 'float',
            'analysis_metadata' => 'array',
            'is_analyzed' => 'boolean',
        ];
    }

    // ─── Relasi ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
