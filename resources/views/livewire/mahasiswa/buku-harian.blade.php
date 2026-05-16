<div class="space-y-6" wire:key="buku-harian">
    {{-- Header Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-800">Buku Harian</h2>
                <p class="text-sm text-slate-500 mt-0.5">Tempat Curhat Aman & Anonim</p>
            </div>
        </div>
        <div class="mt-4 bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-800 leading-relaxed">
            🔒 Tuliskan apa saja yang kamu rasakan hari ini. Ini adalah ruang aman kamu. Sistem kami akan menjaga privasi tulisanmu sambil memantau kesehatan mentalmu secara diam-diam.
        </div>
    </div>

    {{-- Daftar Entri --}}
    @if($entries->isEmpty())
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center">
        <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
        <p class="text-slate-500 font-medium">Buku harianmu masih kosong.</p>
        <p class="text-slate-400 text-sm mt-1">Mulai tulis apa yang kamu rasakan hari ini.</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($entries as $entry)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-medium text-slate-800">Kamu ({{ Auth::user()->username_anonim }})</span>
                        <span class="text-xs text-slate-400">{{ $entry->created_at->translatedFormat('l, d M Y H:i') }}</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $entry->isi }}</p>
                </div>
            </div>
            {{-- AI Reply jika sudah dianalisis --}}
            @if($entry->is_analyzed && $entry->ai_reply)
            <div class="mt-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-blue-700 uppercase tracking-wider">CAMPUS-E AI Assistant</span>
                </div>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $entry->ai_reply }}</p>
                @if($entry->ai_saran && count($entry->ai_saran) > 0)
                <div class="mt-3 bg-white rounded-lg p-3 border border-blue-100">
                    <div class="flex items-center gap-1.5 mb-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                        <span class="text-xs font-semibold text-slate-700">Saran Kegiatan Untukmu:</span>
                    </div>
                    <ul class="space-y-1.5">
                        @foreach($entry->ai_saran as $saran)
                        <li class="flex items-start gap-2 text-sm text-slate-600">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-1.5 flex-shrink-0"></span>
                            {{ $saran }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- AI Typing Indicator --}}
    @if($isAnalyzing)
    <div class="flex items-center gap-2 px-4 py-3">
        <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-medium px-3 py-1.5 rounded-full">
            AI sedang mengetik
            <span class="flex gap-0.5"><span class="w-1 h-1 bg-blue-600 rounded-full animate-bounce" style="animation-delay:0ms"></span><span class="w-1 h-1 bg-blue-600 rounded-full animate-bounce" style="animation-delay:150ms"></span><span class="w-1 h-1 bg-blue-600 rounded-full animate-bounce" style="animation-delay:300ms"></span></span>
        </span>
    </div>
    @endif

    {{-- Latest AI Reply (after submission) --}}
    @if($latestAiReply && !$isAnalyzing)
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
            </div>
            <span class="text-sm font-semibold text-blue-700">CAMPUS-E AI Assistant</span>
        </div>
        <p class="text-sm text-slate-700 leading-relaxed">{{ $latestAiReply['text'] }}</p>
        @if(!empty($latestAiReply['saran']))
        <div class="mt-3 bg-white rounded-lg p-3 border border-blue-100">
            <div class="flex items-center gap-1.5 mb-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                <span class="text-xs font-semibold text-slate-700">Saran Kegiatan Untukmu:</span>
            </div>
            <ul class="space-y-1.5">
                @foreach($latestAiReply['saran'] as $saran)
                <li class="flex items-start gap-2 text-sm text-slate-600"><span class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-1.5 flex-shrink-0"></span>{{ $saran }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    @endif

    {{-- Input Area --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 sticky bottom-16 md:bottom-0 z-30">
        <form wire:submit="simpan">
            <textarea wire:model="newEntry" rows="3" placeholder="Tulis curhatan atau perasaanmu hari ini..."
                      class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none min-h-[44px]"
                      @keydown.enter.exact.prevent="$wire.simpan()"></textarea>
            @error('newEntry')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            <div class="flex justify-end mt-2">
                <button type="submit" {{ empty($newEntry) ? 'disabled' : '' }}
                        class="inline-flex items-center gap-2 bg-blue-700 text-white font-medium text-sm px-5 py-2.5 rounded-lg hover:bg-blue-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                    Simpan Curhatan
                </button>
            </div>
        </form>
    </div>
</div>
