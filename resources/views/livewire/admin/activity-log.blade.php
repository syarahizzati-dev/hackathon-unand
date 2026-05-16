<div class="space-y-4" wire:key="activity-log">
    {{-- ═══ Header Card ═══ --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <h2 class="text-lg sm:text-xl font-bold text-slate-800">Log Aktivitas</h2>
        <p class="text-sm text-slate-500 mt-1">Semua aksi terhadap alert dan identitas mahasiswa tercatat otomatis untuk transparansi dan audit.</p>
    </div>

    {{-- ═══ Daftar Log ═══ --}}
    @forelse($logs as $log)
    @php
        $aksiConfig = match($log->aksi) {
            'identitas_dibuka' => ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>', 'label' => 'Identitas Dibuka', 'color' => 'bg-blue-100 text-blue-700', 'iconBg' => 'bg-blue-100', 'iconColor' => 'text-blue-600'],
            'alert_dibuat' => ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>', 'label' => 'Alert Dibuat', 'color' => 'bg-yellow-100 text-yellow-700', 'iconBg' => 'bg-yellow-100', 'iconColor' => 'text-yellow-600'],
            'alert_ditindaklanjuti' => ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>', 'label' => 'Alert Ditindaklanjuti', 'color' => 'bg-green-100 text-green-700', 'iconBg' => 'bg-green-100', 'iconColor' => 'text-green-600'],
            default => ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>', 'label' => ucfirst(str_replace('_', ' ', $log->aksi)), 'color' => 'bg-slate-100 text-slate-600', 'iconBg' => 'bg-slate-100', 'iconColor' => 'text-slate-500'],
        };
    @endphp
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 hover:shadow-md transition-shadow" wire:key="log-{{ $log->id }}">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 {{ $aksiConfig['iconBg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $aksiConfig['iconColor'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $aksiConfig['icon'] !!}</svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $aksiConfig['color'] }}">{{ $aksiConfig['label'] }}</span>
                    @if($log->severity)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $log->severity === 'kritis' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">{{ strtoupper($log->severity) }}</span>
                    @endif
                </div>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $log->detail }}</p>
                <div class="flex items-center gap-3 mt-2 text-xs text-slate-400 flex-wrap">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        {{ $log->created_at->translatedFormat('d M Y') }}, {{ $log->created_at->format('H.i') }}
                    </span>
                    @if($log->targetUser)
                    <span>{{ $log->targetUser->username_anonim ?? ('STD-' . str_pad($log->target_user_id, 4, '0', STR_PAD_LEFT)) }}</span>
                    @endif
                    <span>Oleh: {{ $log->actor_label ?? ($log->actor ? $log->actor->nama : 'Sistem') }}</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center">
        <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
        <p class="text-slate-500 font-medium">Belum ada log aktivitas.</p>
    </div>
    @endforelse

    {{-- ═══ Ringkasan Aktivitas ═══ --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-4">Ringkasan Aktivitas</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-2xl font-bold text-blue-700">{{ $summary['identitas_dibuka'] }}</p>
                <p class="text-sm text-blue-600 mt-1">Identitas dibuka hari ini</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-2xl font-bold text-yellow-700">{{ $summary['alert_aktif'] }}</p>
                <p class="text-sm text-yellow-600 mt-1">Alert aktif</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-2xl font-bold text-green-700">{{ $summary['ditindaklanjuti'] }}</p>
                <p class="text-sm text-green-600 mt-1">Kasus ditindaklanjuti</p>
            </div>
        </div>
    </div>
</div>
