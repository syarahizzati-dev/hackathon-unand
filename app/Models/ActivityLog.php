<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'aksi',
        'severity',
        'alert_id',
        'target_user_id',
        'actor_id',
        'actor_label',
        'detail',
    ];

    // ─── Append-only: disable update & delete ─────────────────

    /**
     * Override delete — log ini tidak boleh dihapus.
     */
    public function delete(): bool
    {
        throw new \RuntimeException('ActivityLog bersifat append-only — tidak boleh dihapus.');
    }

    /**
     * Override update — log ini tidak boleh diubah setelah dibuat.
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        throw new \RuntimeException('ActivityLog bersifat append-only — tidak boleh diubah.');
    }

    // ─── Relasi ───────────────────────────────────────────────

    /** Alert yang terkait */
    public function alert(): BelongsTo
    {
        return $this->belongsTo(Alert::class);
    }

    /** Mahasiswa yang terkait */
    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /** Admin yang melakukan aksi (null = sistem) */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
