<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CAMPUS-E — Dashboard Mahasiswa</title>
    <meta name="description" content="Dashboard mahasiswa CAMPUS-E untuk monitoring kesehatan mental.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-['Inter',sans-serif] bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col" x-data="{ mobileMenu: false }">

    {{-- ═══ Header Sticky ═══ --}}
    <header class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
        <div class="w-full px-4 sm:px-6 lg:px-0 lg:max-w-5xl lg:mx-auto flex items-center justify-between h-16">
            {{-- Logo + Branding --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-800 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow">
                    C
                </div>
                <div class="hidden sm:block">
                    <h1 class="text-lg font-bold text-slate-800 leading-tight">CAMPUS-E</h1>
                    <p class="text-xs text-slate-500 leading-tight">Dashboard Mahasiswa</p>
                </div>
                <span class="sm:hidden text-lg font-bold text-slate-800">CAMPUS-E</span>
            </div>

            {{-- Real-time Clock --}}
            <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500"
                 x-data="{
                    waktu: '',
                    init() {
                        this.updateClock();
                        setInterval(() => this.updateClock(), 1000);
                    },
                    updateClock() {
                        const now = new Date();
                        const hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                        const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                        const h = String(now.getHours()).padStart(2,'0');
                        const m = String(now.getMinutes()).padStart(2,'0');
                        const s = String(now.getSeconds()).padStart(2,'0');
                        this.waktu = hari[now.getDay()] + ', ' + now.getDate() + ' ' + bulan[now.getMonth()] + ' ' + now.getFullYear() + ' • ' + h + '.' + m + '.' + s + ' WIB';
                    }
                 }">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span x-text="waktu" class="font-medium tabular-nums"></span>
            </div>

            {{-- Tombol Keluar --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-sm text-slate-500 hover:text-red-600 transition-colors">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </header>

    {{-- ═══ Banner Self-Check (slot) ═══ --}}
    @hasSection('banner')
        @yield('banner')
    @endif

    {{-- ═══ Tab Navigasi — Desktop: sticky bar, Mobile: fixed bottom ═══ --}}
    @php
        $tabs = [
            ['route' => 'mahasiswa.dashboard', 'label' => 'Beranda', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />'],
            ['route' => 'mahasiswa.mood', 'label' => 'Mood Tracker', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />'],
            ['route' => 'mahasiswa.buku-harian', 'label' => 'Buku Harian', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />'],
            ['route' => 'mahasiswa.tukar-pikiran', 'label' => 'Tukar Pikiran', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />'],
        ];
    @endphp

    {{-- Desktop tabs --}}
    <nav class="hidden md:block sticky top-16 z-40 bg-white border-b border-slate-200">
        <div class="w-full px-4 sm:px-6 lg:px-0 lg:max-w-5xl lg:mx-auto flex items-center gap-1">
            @foreach ($tabs as $tab)
                <a href="{{ route($tab['route']) }}" wire:navigate
                   class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition-colors
                          {{ request()->routeIs($tab['route']) ? 'border-blue-700 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $tab['icon'] !!}</svg>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>
    </nav>

    {{-- ═══ Main Content ═══ --}}
    <main class="flex-1 w-full px-4 sm:px-6 lg:px-0 lg:max-w-5xl lg:mx-auto py-6 pb-24 md:pb-6">
        @yield('content')
    </main>

    {{-- Mobile bottom tab bar --}}
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-white border-t border-slate-200 safe-area-bottom">
        <div class="flex items-center justify-around h-14">
            @foreach ($tabs as $tab)
                <a href="{{ route($tab['route']) }}" wire:navigate
                   class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1
                          {{ request()->routeIs($tab['route']) ? 'text-blue-700' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $tab['icon'] !!}</svg>
                    <span class="text-[10px] font-medium leading-tight">{{ explode(' ', $tab['label'])[0] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    @livewireScripts
</body>
</html>
