<div class="space-y-6" wire:key="admin-dashboard">
    {{-- ═══ KPI Cards ═══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Mahasiswa --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                </div>
                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/></svg>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ number_format($totalMahasiswa) }}</p>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Total Mahasiswa</p>
        </div>
        {{-- Zona Aman --}}
        <div class="bg-green-50 rounded-xl border border-green-200 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-green-700">{{ number_format($zonaAman) }}</p>
            <p class="text-xs sm:text-sm text-green-600 mt-1">Zona Aman <span class="text-green-500">({{ $zonaAmanPct }}%)</span></p>
        </div>
        {{-- Waspada --}}
        <div class="bg-yellow-50 rounded-xl border border-yellow-200 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-yellow-700">{{ number_format($waspada) }}</p>
            <p class="text-xs sm:text-sm text-yellow-600 mt-1">Waspada <span class="text-yellow-500">({{ $waspadaPct }}%)</span></p>
        </div>
        {{-- Kritis --}}
        <div class="bg-red-50 rounded-xl border border-red-200 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-red-700">{{ number_format($kritis) }}</p>
            <p class="text-xs sm:text-sm text-red-600 mt-1">Kritis <span class="text-red-500">({{ $kritisPct }}%)</span></p>
        </div>
    </div>

    {{-- ═══ Distribusi Status Mental ═══ --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-4">Distribusi Status Mental</h3>
        <div class="space-y-4">
            @php $total = max($zonaAman + $waspada + $kritis, 1); @endphp
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-600 w-20 flex-shrink-0">Zona Aman</span>
                <div class="flex-1 bg-slate-100 rounded-full h-3 overflow-hidden"><div class="bg-green-500 h-full rounded-full transition-all duration-700" style="width:{{ ($zonaAman/$total)*100 }}%"></div></div>
                <span class="text-sm font-medium text-slate-700 w-12 text-right">{{ $zonaAman }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-600 w-20 flex-shrink-0">Waspada</span>
                <div class="flex-1 bg-slate-100 rounded-full h-3 overflow-hidden"><div class="bg-yellow-500 h-full rounded-full transition-all duration-700" style="width:{{ ($waspada/$total)*100 }}%"></div></div>
                <span class="text-sm font-medium text-slate-700 w-12 text-right">{{ $waspada }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-600 w-20 flex-shrink-0">Kritis</span>
                <div class="flex-1 bg-slate-100 rounded-full h-3 overflow-hidden"><div class="bg-red-500 h-full rounded-full transition-all duration-700" style="width:{{ ($kritis/$total)*100 }}%"></div></div>
                <span class="text-sm font-medium text-slate-700 w-12 text-right">{{ $kritis }}</span>
            </div>
        </div>
    </div>

    {{-- ═══ Tren Minggu Ini + Jurusan Terbanyak ═══ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Tren --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-4">Tren Minggu Ini</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></div>
                        <span class="text-sm text-slate-600">Self-check completion</span>
                    </div>
                    <span class="text-sm font-semibold {{ $selfCheckTrend['direction'] === 'up' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $selfCheckTrend['direction'] === 'up' ? '+' : '-' }}{{ $selfCheckTrend['value'] }}%
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg></div>
                        <span class="text-sm text-slate-600">Buku harian sessions</span>
                    </div>
                    <span class="text-sm font-semibold {{ $diaryTrend['direction'] === 'up' ? 'text-blue-600' : 'text-red-600' }}">
                        {{ $diaryTrend['direction'] === 'up' ? '+' : '-' }}{{ $diaryTrend['value'] }}%
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg></div>
                        <span class="text-sm text-slate-600">Forum posts</span>
                    </div>
                    <span class="text-sm font-semibold {{ $forumTrend['direction'] === 'up' ? 'text-purple-600' : 'text-red-600' }}">
                        {{ $forumTrend['direction'] === 'up' ? '+' : '-' }}{{ $forumTrend['value'] }}%
                    </span>
                </div>
            </div>
        </div>
        {{-- Jurusan Terbanyak --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-4">Jurusan Terbanyak</h3>
            @if(count($topJurusan) > 0)
            <div class="space-y-3">
                @foreach($topJurusan as $j)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600 truncate mr-3">{{ $j['nama'] }}</span>
                    <span class="text-sm font-semibold text-slate-800 flex-shrink-0">{{ number_format($j['total']) }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-slate-400">Belum ada data jurusan.</p>
            @endif
        </div>
    </div>
</div>
