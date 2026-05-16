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
        <div class="min-h-screen flex flex-col items-center justify-center px-4 sm:px-6"
             style="background-image: linear-gradient(150deg, #eff6ff 0%, #ffffff 50%, #eff6ff 100%);">

            <!-- Logo + Branding -->
            <div class="flex flex-col items-center mb-8">
                <img src="{{ asset('images/logo-campus-e.png') }}" alt="CAMPUS-E Logo"
                     class="w-16 h-16 sm:w-20 sm:h-20 md:w-[73px] md:h-[73px] mb-4 object-contain drop-shadow-lg" />
                <h1 class="text-2xl sm:text-[30px] font-bold text-slate-800 leading-9 text-center">CAMPUS-E</h1>
                <p class="text-slate-500 text-sm sm:text-base mt-1 text-center">Sistem Prediksi Resiko Depresi Mahasiswa</p>
            </div>

            <!-- Form Card -->
            <div class="w-full sm:max-w-md lg:max-w-lg bg-white border border-slate-200 rounded-xl shadow-sm p-6 sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
