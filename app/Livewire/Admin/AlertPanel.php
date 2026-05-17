<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Alert;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class AlertPanel extends Component
{
    public array $openedIdentities = [];

    // ─── Modal State ─────────────────────────────────────────
    public bool $showHandleModal = false;
    public ?int $handlingAlertId = null;
    public string $catatanTindakLanjut = '';

    public function openIdentity(int $alertId): void
    {
        $alert = Alert::findOrFail($alertId);

        // Update alert
        $alert->identity_opened = true;
        $alert->opened_by = Auth::id();
        $alert->opened_at = now();
        $alert->save();

        // Log aktivitas
        ActivityLog::create([
            'aksi'           => 'identitas_dibuka',
            'severity'       => $alert->label >= 4 ? 'kritis' : 'waspada',
            'alert_id'       => $alert->id,
            'target_user_id' => $alert->user_id,
            'actor_id'       => Auth::id(),
            'actor_label'    => Auth::user()->nama ?? 'Admin',
            'detail'         => 'Identitas dibuka karena deteksi risiko: ' . ($alert->cuplikan_teks ? mb_substr($alert->cuplikan_teks, 0, 80) : 'N/A'),
        ]);

        $this->dispatch('activity-log-updated');
        $this->openedIdentities[$alertId] = true;
    }

    public function hideIdentity(int $alertId): void
    {
        unset($this->openedIdentities[$alertId]);
    }

    /**
     * Buka modal konfirmasi tindak lanjut.
     */
    public function openHandleModal(int $alertId): void
    {
        $this->handlingAlertId = $alertId;
        $this->catatanTindakLanjut = '';
        $this->resetValidation();
        $this->showHandleModal = true;
    }

    /**
     * Tutup modal tanpa aksi.
     */
    public function closeHandleModal(): void
    {
        $this->showHandleModal = false;
        $this->handlingAlertId = null;
        $this->catatanTindakLanjut = '';
        $this->resetValidation();
    }

    /**
     * Konfirmasi tindak lanjut — simpan ke DB dengan catatan admin.
     */
    public function confirmHandle(): void
    {
        $this->validate([
            'catatanTindakLanjut' => 'required|string|min:5|max:1000',
        ], [
            'catatanTindakLanjut.required' => 'Catatan tindak lanjut wajib diisi.',
            'catatanTindakLanjut.min'      => 'Catatan minimal 5 karakter.',
        ]);

        $alert = Alert::findOrFail($this->handlingAlertId);

        $alert->is_handled = true;
        $alert->handled_by = Auth::id();
        $alert->handled_at = now();
        $alert->save();

        // Log aktivitas dengan catatan spesifik dari admin
        ActivityLog::create([
            'aksi'           => 'alert_ditindaklanjuti',
            'severity'       => $alert->label >= 4 ? 'kritis' : 'waspada',
            'alert_id'       => $alert->id,
            'target_user_id' => $alert->user_id,
            'actor_id'       => Auth::id(),
            'actor_label'    => Auth::user()->nama ?? 'Admin',
            'detail'         => $this->catatanTindakLanjut,
        ]);

        // Bersihkan state
        unset($this->openedIdentities[$this->handlingAlertId]);
        $this->closeHandleModal();

        // Notifikasi sukses
        session()->flash('alert-success', 'Berhasil ditindaklanjuti.');

        $this->dispatch('activity-log-updated');
    }

    public function render()
    {
        $alerts = Alert::where('is_handled', false)
            ->with('user:id,nama,nim,email,no_telepon,jurusan,program_studi,kontak_darurat,username_anonim')
            ->orderByDesc('label')
            ->orderByDesc('created_at')
            ->get();

        $handledCount = Alert::where('is_handled', true)->count();

        return view('livewire.admin.alert-panel', [
            'alerts' => $alerts,
            'handledCount' => $handledCount,
        ]);
    }
}
