<div>
    {{-- ═══ Self-Check Banner ═══ --}}
    @if($showBanner)
    <div class="bg-blue-800 w-full" x-data x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="w-full px-4 sm:px-6 lg:px-0 lg:max-w-5xl lg:mx-auto flex items-center justify-between py-3">
            <div class="flex items-center gap-3">
                {{-- Check Circle Icon --}}
                <svg class="w-5 h-5 text-white flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <div>
                    <p class="text-white font-medium text-sm sm:text-base">Self-check harian tersedia</p>
                    <p class="text-white/80 text-xs sm:text-sm">Hanya 30 detik untuk cek kondisi kamu hari ini</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button wire:click="mulai"
                        class="bg-white text-blue-800 font-medium text-sm px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors shadow-sm">
                    Mulai
                </button>
                <button wire:click="dismiss"
                        class="text-white/70 hover:text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══ Self-Check Form (Inline Modal) ═══ --}}
    @if($showForm)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm"
         x-data x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="w-full max-w-lg mx-4">
            @livewire('mahasiswa.self-check-form', key('self-check-form'))
            <button wire:click="closeForm"
                    class="mt-3 w-full text-center text-white/80 hover:text-white text-sm py-2 transition-colors">
                Tutup
            </button>
        </div>
    </div>
    @endif
</div>
