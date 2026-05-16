<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Alert;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class AlertPanel extends Component
{
    public array $openedIdentities = [];

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

    public function handleAlert(int $alertId): void
    {
        $alert = Alert::findOrFail($alertId);

        $alert->is_handled = true;
        $alert->handled_by = Auth::id();
        $alert->handled_at = now();
        $alert->save();

        // Log aktivitas
        ActivityLog::create([
            'aksi'           => 'alert_ditindaklanjuti',
            'severity'       => $alert->label >= 4 ? 'kritis' : 'waspada',
            'alert_id'       => $alert->id,
            'target_user_id' => $alert->user_id,
            'actor_id'       => Auth::id(),
            'actor_label'    => Auth::user()->nama ?? 'Admin',
            'detail'         => 'Alert telah ditindaklanjuti oleh admin',
        ]);

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
