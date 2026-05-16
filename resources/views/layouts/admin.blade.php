<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>CAMPUS-E Admin — Sistem Monitoring Kesehatan Mental</title>
    <meta name="description" content="Panel admin CAMPUS-E untuk monitoring kesehatan mental mahasiswa.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-['Inter',sans-serif] bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col">

    {{-- ═══ Header Sticky ═══ --}}
    <header class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
        <div class="w-full px-4 sm:px-6 lg:px-0 lg:max-w-5xl lg:mx-auto flex items-center justify-between h-16">
            {{-- Logo + Branding --}}
            <div class="flex items-center gap-2 sm:gap-3">
                <img src="{{ asset('images/logo-campus-e.png') }}" alt="CAMPUS-E Logo"
                     class="w-8 h-8 sm:w-10 sm:h-10 object-contain flex-shrink-0" />
                <div class="hidden sm:block">
                    <h1 class="text-lg font-bold text-slate-800 leading-tight">CAMPUS-E Admin</h1>
                    <p class="text-xs text-slate-500 leading-tight">Sistem Monitoring Kesehatan Mental</p>
                </div>
                <span class="sm:hidden text-lg font-bold text-slate-800">Admin</span>
            </div>

            {{-- Real-time Clock --}}
            <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500" x-data="{
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
                <span x-text="waktu" class="font-medium tabular-nums"></span>
            </div>

            {{-- Tombol Keluar --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2 text-sm text-slate-500 hover:text-red-600 transition-colors">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </header>

    {{-- ═══ Tab Navigasi ═══ --}}
    @php
        $tabs = [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />'],
            ['route' => 'admin.alert', 'label' => 'Alert Krisis', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />'],
            ['route' => 'admin.log', 'label' => 'Log Aktivitas', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />'],
        ];
    @endphp

    {{-- Desktop tabs --}}
    <nav class="hidden md:block sticky top-16 z-40 bg-white border-b border-slate-200">
        <div class="w-full px-4 sm:px-6 lg:px-0 lg:max-w-5xl lg:mx-auto flex items-center gap-1">
            @foreach ($tabs as $tab)
                <a href="{{ route($tab['route']) }}" wire:navigate
                    class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition-colors
                                  {{ request()->routeIs($tab['route']) ? 'border-blue-700 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">{!! $tab['icon'] !!}</svg>
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
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">{!! $tab['icon'] !!}</svg>
                    <span class="text-[10px] font-medium leading-tight">{{ explode(' ', $tab['label'])[0] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    @livewireScripts
</body>

</html>