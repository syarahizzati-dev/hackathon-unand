<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alert extends Model
{
    protected $fillable = [
        'user_id',
        'sumber',
        'sumber_id',
        'label',
        'risk_level',
        'confidence',
        'kata_kunci',
        'cuplikan_teks',
        'admin_steps',
        'analysis_metadata',
        'is_handled',
        'handled_by',
        'handled_at',
        'identity_opened',
        'opened_by',
        'opened_at',
    ];

    protected function casts(): array
    {
        return [
            'kata_kunci'      => 'array',
            'admin_steps'     => 'array',
            'analysis_metadata' => 'array',
            'label'           => 'integer',
            'confidence'      => 'float',
            'is_handled'      => 'boolean',
            'identity_opened' => 'boolean',
            'handled_at'      => 'datetime',
            'opened_at'       => 'datetime',
        ];
    }

    // ─── Relasi ───────────────────────────────────────────────

    /** Mahasiswa yang terdeteksi */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Admin yang menindaklanjuti */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /** Admin yang membuka identitas */
    public function opener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    /** Log aktivitas terkait alert ini */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
