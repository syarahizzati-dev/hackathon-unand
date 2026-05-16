<div class="space-y-6" wire:key="mood-tracker">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800">Mood Tracker</h2>
                <div class="flex items-center gap-2">
                    <button wire:click="previousMonth" class="p-2 rounded-lg hover:bg-slate-100 transition-colors text-slate-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    </button>
                    <span class="text-sm font-medium text-slate-800 min-w-[120px] text-center">{{ $monthName }}</span>
                    <button wire:click="nextMonth" class="p-2 rounded-lg hover:bg-slate-100 transition-colors text-slate-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-7 gap-1 sm:gap-2 mb-2">
                @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $hari)
                <div class="text-center text-xs sm:text-sm font-medium text-slate-500 py-2">{{ $hari }}</div>
                @endforeach
            </div>
            <div class="grid grid-cols-7 gap-1 sm:gap-2">
                @foreach($calendar as $cell)
                    @if($cell === null)
                    <div class="aspect-square"></div>
                    @else
                    @php
                        $moodClass = 'bg-slate-100 text-slate-500';
                        if ($cell['mood']) {
                            $moodClass = match($cell['mood']['mood']) {
                                'baik' => 'bg-[#00c950] text-white',
                                'waspada' => 'bg-[#fdc700] text-white',
                                'kritis' => 'bg-[#fb2c36] text-white',
                                default => 'bg-slate-100 text-slate-500',
                            };
                        }
                    @endphp
                    <div class="aspect-square rounded-lg flex items-center justify-center text-xs sm:text-sm font-medium transition-all duration-200 cursor-default relative group {{ $cell['isToday'] ? 'ring-2 ring-blue-500 ring-offset-1' : '' }} {{ $moodClass }}">
                        {{ $cell['day'] }}
                        @if($cell['mood'])
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-slate-800 text-white text-[10px] rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                            {{ $cell['day'] }} — {{ ucfirst($cell['mood']['mood']) }}
                        </div>
                        @endif
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-4">Keterangan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex items-center gap-3"><div class="w-6 h-6 bg-[#00c950] rounded flex-shrink-0"></div><div><p class="text-sm font-medium text-slate-800">Baik</p><p class="text-xs text-slate-500">Kondisi positif</p></div></div>
            <div class="flex items-center gap-3"><div class="w-6 h-6 bg-[#fdc700] rounded flex-shrink-0"></div><div><p class="text-sm font-medium text-slate-800">Waspada</p><p class="text-xs text-slate-500">Perlu perhatian</p></div></div>
            <div class="flex items-center gap-3"><div class="w-6 h-6 bg-[#fb2c36] rounded flex-shrink-0"></div><div><p class="text-sm font-medium text-slate-800">Kritis</p><p class="text-xs text-slate-500">Butuh bantuan</p></div></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-4">Statistik Bulan Ini</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4"><p class="text-2xl font-bold text-green-700">{{ $statistics['baik'] }} hari</p><p class="text-sm text-green-600 mt-1">Kondisi Baik</p></div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4"><p class="text-2xl font-bold text-yellow-700">{{ $statistics['waspada'] }} hari</p><p class="text-sm text-yellow-600 mt-1">Waspada</p></div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4"><p class="text-2xl font-bold text-red-700">{{ $statistics['kritis'] }} hari</p><p class="text-sm text-red-600 mt-1">Kritis</p></div>
        </div>
    </div>
</div>
