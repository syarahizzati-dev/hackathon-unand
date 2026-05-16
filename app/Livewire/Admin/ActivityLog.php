<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ActivityLog as ActivityLogModel;
use App\Models\Alert;
use Illuminate\Support\Facades\DB;

class ActivityLog extends Component
{
    public function render()
    {
        $logs = ActivityLogModel::with(['alert', 'targetUser:id,username_anonim,nim', 'actor:id,nama'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        // Ringkasan
        $identitasDibukaHariIni = ActivityLogModel::where('aksi', 'identitas_dibuka')
            ->whereDate('created_at', today())
            ->count();

        $alertAktif = Alert::where('is_handled', false)->count();

        $kasusDitindaklanjuti = ActivityLogModel::where('aksi', 'alert_ditindaklanjuti')
            ->whereDate('created_at', today())
            ->count();

        return view('livewire.admin.activity-log', [
            'logs' => $logs,
            'summary' => [
                'identitas_dibuka' => $identitasDibukaHariIni,
                'alert_aktif'      => $alertAktif,
                'ditindaklanjuti'  => $kasusDitindaklanjuti,
            ],
        ]);
    }
}
