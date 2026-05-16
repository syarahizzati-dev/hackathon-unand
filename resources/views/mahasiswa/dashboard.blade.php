@extends('layouts.mahasiswa')

@section('content')
    {{-- Kartu Sambutan --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800 mb-2">Selamat Datang! 👋</h2>
        <p class="text-slate-500 text-sm leading-relaxed">
            CAMPUS-E adalah sistem yang membantu kamu memantau kesehatan mental dengan aman dan anonim.
        </p>
    </div>

    {{-- Statistik Singkat --}}
    <div class="grid grid-cols-3 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-sm">
            <p class="text-2xl sm:text-3xl font-bold text-blue-700">30s</p>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Self-check harian</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-sm">
            <p class="text-2xl sm:text-3xl font-bold text-blue-700">100%</p>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Anonim & aman</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 text-center shadow-sm">
            <p class="text-2xl sm:text-3xl font-bold text-blue-700">24/7</p>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Tempat curhat aman</p>
        </div>
    </div>

    {{-- Aktivitas Terakhir --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Aktivitas Terakhir</h3>
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-green-500 flex-shrink-0"></span>
                <div>
                    <p class="text-sm font-medium text-slate-700">Self-check selesai</p>
                    <p class="text-xs text-slate-400">Hari ini, 08:30</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                <div>
                    <p class="text-sm font-medium text-slate-700">Menulis Buku Harian</p>
                    <p class="text-xs text-slate-400">Kemarin, 20:15</p>
                </div>
            </div>
        </div>
    </div>
@endsection
