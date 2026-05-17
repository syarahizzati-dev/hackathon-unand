<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <title>CAMPUS-E — Sistem Prediksi Resiko Depresi Mahasiswa</title>
        <meta name="description" content="CAMPUS-E adalah sistem prediksi resiko depresi mahasiswa berbasis AI IndoBERT.">

        <!-- Fonts: Inter (sesuai Figma) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-['Inter',sans-serif] text-slate-900 antialiased">
        <div class="relative min-h-screen overflow-hidden flex flex-col items-center justify-center px-4 py-8 sm:px-6"
             style="background-image: radial-gradient(circle at top left, rgba(37, 99, 235, 0.16), transparent 32rem), radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.12), transparent 28rem), linear-gradient(150deg, #eff6ff 0%, #ffffff 48%, #f8fafc 100%);">
            <div class="pointer-events-none absolute inset-0 opacity-[0.035]" style="background-image: radial-gradient(#1e293b 1px, transparent 1px); background-size: 22px 22px;"></div>

            <!-- Logo + Branding -->
            <div class="relative flex flex-col items-center mb-8">
                <img src="{{ asset('images/logo-campus-e.png') }}" alt="CAMPUS-E Logo"
                     class="w-16 h-16 sm:w-20 sm:h-20 mb-4 object-contain drop-shadow-lg" />
                <h1 class="text-2xl sm:text-[30px] font-bold text-slate-800 leading-9 text-center">CAMPUS-E</h1>
                <p class="text-slate-500 text-sm sm:text-base mt-1 text-center">Sistem Prediksi Resiko Depresi Mahasiswa</p>
            </div>

            @php($authWidth = request()->routeIs('register') ? 'sm:max-w-2xl lg:max-w-3xl' : 'sm:max-w-md lg:max-w-lg')

            <!-- Form Card -->
            <div class="relative w-full {{ $authWidth }} bg-white/95 backdrop-blur border border-blue-100/80 rounded-2xl shadow-xl shadow-blue-900/5 p-5 sm:p-8">
                {{ $slot }}
            </div>

            <p class="relative mt-5 text-center text-xs sm:text-sm text-slate-500">
                Privasi dijaga <span class="mx-1 text-slate-300">•</span> Ruang curhat anonim <span class="mx-1 text-slate-300">•</span> Alert untuk keselamatan
            </p>
        </div>
    </body>
</html>
