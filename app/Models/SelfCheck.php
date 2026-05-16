<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelfCheck extends Model
{
    protected $table = 'self_checks';

    protected $fillable = [
        'user_id',
        'jawaban_1',
        'jawaban_2',
        'jawaban_3',
        'jawaban_4',
        'jawaban_5',
        'skor_total',
        'teks_gabung',
        'label',
        'risk_level',
        'confidence',
    ];

    protected function casts(): array
    {
        return [
            'jawaban_1'  => 'integer',
            'jawaban_2'  => 'integer',
            'jawaban_3'  => 'integer',
            'jawaban_4'  => 'integer',
            'jawaban_5'  => 'integer',
            'skor_total' => 'integer',
            'label'      => 'integer',
            'confidence' => 'float',
        ];
    }

    // ─── Relasi ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
