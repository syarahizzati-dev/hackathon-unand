<div class="space-y-4" wire:key="alert-panel">
    {{-- ═══ Flash Notification ═══ --}}
    @if(session('alert-success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3 shadow-sm"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        </div>
        <p class="text-sm font-medium text-green-700">{{ session('alert-success') }}</p>
        <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif
    {{-- ═══ Banner Atas (Merah) ═══ --}}
    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-xl p-5 sm:p-6 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-white">Peringatan Real-time</h2>
                <p class="text-sm text-red-100 mt-1 leading-relaxed">Sistem mendeteksi kata kunci berbahaya dari curhatan mahasiswa di buku harian. Segera tindak lanjuti kasus dengan tingkat keparahan tinggi.</p>
            </div>
        </div>
    </div>

    {{-- ═══ Daftar Alert ═══ --}}
    @forelse($alerts as $alert)
    <div class="bg-white rounded-xl border-l-4 shadow-sm overflow-hidden {{ $alert->label >= 4 ? 'border-l-red-500 border border-red-200' : ($alert->label >= 3 ? 'border-l-red-400 border border-red-100' : 'border-l-yellow-400 border border-yellow-100') }}" wire:key="alert-{{ $alert->id }}">
        <div class="p-5">
            {{-- Header: Badge + Waktu --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-3">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $alert->label >= 3 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                    {{ $alert->label >= 3 ? 'KRITIS' : 'WASPADA' }}
                </span>
                <span class="flex items-center gap-1 text-xs text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    {{ $alert->created_at->translatedFormat('d M Y') }}, {{ $alert->created_at->format('H.i') }}
                </span>
            </div>

            {{-- Kata Kunci --}}
            @if($alert->kata_kunci && count($alert->kata_kunci) > 0)
            <div class="flex flex-wrap gap-1.5 mb-3">
                @foreach($alert->kata_kunci as $kw)
                <span class="inline-block bg-red-50 text-red-600 text-xs px-2 py-0.5 rounded-full border border-red-100">"{{ $kw }}"</span>
                @endforeach
            </div>
            @endif

            {{-- Cuplikan Teks --}}
            @if($alert->cuplikan_teks)
            <p class="text-sm text-slate-700 bg-slate-50 rounded-lg p-3 border border-slate-100 leading-relaxed italic break-words">"{{ $alert->cuplikan_teks }}"</p>
            @endif

            @if($alert->admin_steps && count($alert->admin_steps) > 0)
            <div class="mt-4 bg-red-50 border border-red-100 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Z"/></svg>
                    <p class="text-sm font-semibold text-red-700">Langkah yang Disarankan</p>
                </div>
                <ol class="space-y-2">
                    @foreach($alert->admin_steps as $step)
                    <li class="flex gap-2 text-sm text-red-800 leading-relaxed">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-red-100 text-red-700 text-xs font-bold flex items-center justify-center">{{ $loop->iteration }}</span>
                        <span class="break-words">{{ $step }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>
            @endif

            {{-- Aksi --}}
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 mt-4">
                @if(!isset($openedIdentities[$alert->id]))
                <button wire:click="openIdentity({{ $alert->id }})" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-blue-800 transition-colors min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    Buka Identitas Darurat
                </button>
                @else
                <button wire:click="hideIdentity({{ $alert->id }})" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 bg-slate-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-slate-700 transition-colors min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                    Sembunyikan Identitas
                </button>
                @endif

                <button wire:click="openHandleModal({{ $alert->id }})"
                        class="inline-flex w-full sm:w-auto items-center justify-center gap-2 bg-green-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-green-700 transition-colors min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    Tindaklanjuti
                </button>
            </div>
        </div>

        {{-- ═══ Kartu Identitas (jika dibuka) ═══ --}}
        @if(isset($openedIdentities[$alert->id]) && $alert->user)
        <div class="border-t border-blue-200 bg-blue-50 p-5" x-data x-transition>
            <div class="flex items-center gap-2 mb-4 text-xs text-blue-600">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                Identitas dibuka pada {{ $alert->opened_at ? $alert->opened_at->translatedFormat('d M Y') . ', ' . $alert->opened_at->format('H.i') : now()->translatedFormat('d M Y') . ', ' . now()->format('H.i') }}
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div><p class="text-xs text-slate-500">Nama Lengkap</p><p class="text-sm font-medium text-slate-800 break-words">{{ $alert->user->nama ?? '-' }}</p></div>
                <div><p class="text-xs text-slate-500">NIM</p><p class="text-sm font-medium text-slate-800 break-words">{{ $alert->user->nim ?? '-' }}</p></div>
                <div><p class="text-xs text-slate-500">Jurusan / Program Studi</p><p class="text-sm font-medium text-slate-800 break-words">{{ $alert->user->jurusan ?? '-' }} — {{ $alert->user->program_studi ?? '-' }}</p></div>
                <div><p class="text-xs text-slate-500">Email</p><p class="text-sm font-medium text-slate-800 break-all">{{ $alert->user->email ?? '-' }}</p></div>
                <div><p class="text-xs text-slate-500">Telepon Mahasiswa</p><p class="text-sm font-medium text-slate-800 break-words">{{ $alert->user->no_telepon ?? '-' }}</p></div>
                <div><p class="text-xs text-slate-500 text-red-600 font-semibold">Kontak Darurat</p><p class="text-sm font-bold text-red-700 break-words">{{ $alert->user->kontak_darurat ?? '-' }}</p></div>
            </div>
            {{-- Tombol Aksi Cepat --}}
            <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-blue-200">
                @if($alert->user->no_telepon)
                <a href="tel:{{ $alert->user->no_telepon }}" class="inline-flex items-center gap-2 bg-green-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-green-700 transition-colors min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                    Hubungi Mahasiswa
                </a>
                @endif
                @if($alert->user->kontak_darurat)
                <a href="tel:{{ $alert->user->kontak_darurat }}" class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-red-700 transition-colors min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                    Hubungi Darurat
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        </div>
        <p class="text-slate-600 font-medium">Tidak ada alert aktif</p>
        <p class="text-slate-400 text-sm mt-1">Semua kasus telah ditindaklanjuti. {{ $handledCount }} alert total telah di-handle.</p>
    </div>
    @endforelse

    {{-- ═══ Modal Konfirmasi Tindak Lanjut ═══ --}}
    @if($showHandleModal)
    @php $modalAlert = \App\Models\Alert::with('user:id,username_anonim')->find($handlingAlertId); @endphp
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-data x-transition>
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeHandleModal"></div>

        {{-- Modal Content --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto z-10">
            {{-- Header --}}
            <div class="sticky top-0 bg-white border-b border-slate-200 rounded-t-2xl px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Konfirmasi Tindak Lanjut</h3>
                        <p class="text-xs text-slate-500">Catat tindakan yang telah dilakukan</p>
                    </div>
                </div>
                <button wire:click="closeHandleModal" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">
                {{-- Ringkasan Alert --}}
                @if($modalAlert)
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $modalAlert->label >= 3 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $modalAlert->label >= 3 ? 'KRITIS' : 'WASPADA' }}
                        </span>
                        <span class="text-xs text-slate-400">{{ $modalAlert->created_at->translatedFormat('d M Y') }}, {{ $modalAlert->created_at->format('H.i') }}</span>
                    </div>
                    @if($modalAlert->cuplikan_teks)
                    <p class="text-sm text-slate-600 italic leading-relaxed break-words">"{{ mb_substr($modalAlert->cuplikan_teks, 0, 120) }}{{ mb_strlen($modalAlert->cuplikan_teks) > 120 ? '...' : '' }}"</p>
                    @endif
                    <p class="text-xs text-slate-400 mt-2">Mahasiswa: {{ $modalAlert->user->username_anonim ?? 'Anonim' }}</p>
                </div>
                @endif

                {{-- Form Catatan --}}
                <div>
                    <label for="catatan-tindak-lanjut" class="block text-sm font-medium text-slate-700 mb-2">
                        Catatan Tindak Lanjut <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="catatanTindakLanjut" id="catatan-tindak-lanjut" rows="4"
                              placeholder="Contoh: Mahasiswa sudah dihubungi via telepon. Dijadwalkan konseling pada hari Senin, 19 Mei 2026 pukul 10.00 WIB dengan konselor kampus."
                              class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"></textarea>
                    @error('catatanTindakLanjut')
                    <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                        {{ $message }}
                    </p>
                    @enderror
                    <p class="text-xs text-slate-400 mt-1.5">Minimal 5 karakter. Catatan ini akan tersimpan di log aktivitas.</p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="sticky bottom-0 bg-white border-t border-slate-200 rounded-b-2xl px-6 py-4 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                <button wire:click="closeHandleModal"
                        class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors min-h-[44px]">
                    Batal
                </button>
                <button wire:click="confirmHandle"
                        wire:loading.attr="disabled"
                        wire:target="confirmHandle"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="confirmHandle">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    </span>
                    <span wire:loading wire:target="confirmHandle">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </span>
                    <span wire:loading.remove wire:target="confirmHandle">Konfirmasi Tindak Lanjut</span>
                    <span wire:loading wire:target="confirmHandle">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
