<div class="bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden max-h-[calc(100vh-7rem)] sm:max-h-[calc(100vh-3rem)] overflow-y-auto" x-data
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">

    @if(!$isComplete)
    {{-- ═══ Header ═══ --}}
    <div class="p-6 pb-4">
        <h2 class="text-xl font-bold text-slate-800">Self-check Harian</h2>
        <p class="text-sm text-slate-500 mt-1">Pertanyaan {{ $currentQuestion }} dari 5</p>

        {{-- Progress Bar --}}
        <div class="mt-4 h-2 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-blue-700 rounded-full transition-all duration-500 ease-out"
                 style="width: {{ ($currentQuestion / 5) * 100 }}%"></div>
        </div>
    </div>

    {{-- ═══ Pertanyaan ═══ --}}
    <div class="px-6 pb-2">
        <p class="text-lg font-medium text-slate-800 leading-relaxed">
            {{ $pertanyaan[$currentQuestion]['teks'] }}
        </p>
    </div>

    {{-- ═══ Pilihan Jawaban ═══ --}}
    <div class="px-6 pb-6 space-y-3 mt-2">
        @foreach($opsiJawaban as $opsi)
        <button wire:click="selectAnswer({{ $currentQuestion }}, {{ $opsi['value'] }})"
                class="w-full {{ $opsi['bg'] }} {{ $opsi['color'] }} font-medium py-3.5 px-5 rounded-xl
                       transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]
                       shadow-sm hover:shadow-md text-left flex items-center justify-between group min-h-[44px]">
            <span>{{ $opsi['label'] }}</span>
            <svg class="w-5 h-5 opacity-0 group-hover:opacity-100 transition-opacity" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </button>
        @endforeach
    </div>

    {{-- ═══ Indikator Pertanyaan ═══ --}}
    <div class="px-6 pb-6 flex items-center justify-center gap-2">
        @for($i = 1; $i <= 5; $i++)
        <div class="w-2.5 h-2.5 rounded-full transition-all duration-300 {{ $i < $currentQuestion ? 'bg-blue-700' : ($i === $currentQuestion ? 'bg-blue-700 ring-2 ring-blue-200 scale-125' : 'bg-slate-200') }}"></div>
        @endfor
    </div>

    @if($readyToSubmit)
    <div class="px-6 pb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-3">
            <p class="text-sm font-medium text-blue-800">Semua jawaban sudah terisi.</p>
            <p class="text-xs text-blue-600 mt-1">Tekan tombol kirim untuk menyimpan self-check dan memperbarui mood tracker.</p>
        </div>
        <button wire:click="submit"
                class="w-full bg-blue-800 hover:bg-blue-900 text-white font-semibold py-3.5 px-5 rounded-xl transition-colors min-h-[44px]">
            Kirim Self-check
        </button>
    </div>
    @endif

    @else
    {{-- ═══ Layar Selesai ═══ --}}
    <div class="p-8 text-center" x-data x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-2">Selesai!</h3>
        <p class="text-slate-500">Terima kasih sudah mengisi self-check hari ini</p>
        @if($skorTotal)
        <div class="mt-4 space-y-2">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium
                        {{ $skorTotal >= 20 ? 'bg-green-100 text-green-700' : ($skorTotal >= 13 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                <span>Skor: {{ $skorTotal }}/25</span>
            </div>
            <p class="text-xs text-slate-400">
                @if($skorTotal >= 21) Kondisi: Normal — Terus jaga kesehatanmu! 💚
                @elseif($skorTotal >= 17) Kondisi: Sedikit Lelah — Istirahat yang cukup ya 💛
                @elseif($skorTotal >= 13) Kondisi: Perlu Perhatian — Jangan ragu untuk cerita 🧡
                @elseif($skorTotal >= 9) Kondisi: Waspada — Kami ada untukmu ❤️
                @else Kondisi: Butuh Bantuan — Segera hubungi konselor 🆘
                @endif
            </p>
        </div>
        @endif
    </div>
    @endif
</div>
