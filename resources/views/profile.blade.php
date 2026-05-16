@extends('layouts.mahasiswa')

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6">
            <h2 class="text-lg sm:text-xl font-bold text-slate-800">Profil</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola informasi akun dan keamanan password kamu.</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
@endsection
