<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     * Mahasiswa → /student-dashboard, Admin ditolak.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        // Tolak admin login di halaman mahasiswa
        if (auth()->user()->is_admin) {
            auth()->logout();
            Session::invalidate();
            Session::regenerateToken();
            $this->addError('form.email', 'Akun admin tidak bisa login di sini. Gunakan halaman admin.');
            return;
        }

        Session::regenerate();

        $this->redirectIntended(default: route('mahasiswa.dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-6">
        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700 ring-1 ring-blue-100">
            Portal Mahasiswa
        </span>
        <h2 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">Masuk ke ruang dukunganmu</h2>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">Lanjutkan self-check, buku harian, dan ruang berbagi anonim dengan aman.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-800 mb-2">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>
                <input wire:model="form.email" id="email" type="email"
                       class="block w-full pl-10 pr-4 py-3 border border-slate-200 rounded-lg text-base placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="email@example.com" required autofocus autocomplete="username" />
            </div>
            @error('form.email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4" x-data="{ showPassword: false }">
            <label for="password" class="block text-sm font-medium text-slate-800 mb-2">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <input wire:model="form.password" id="password"
                       :type="showPassword ? 'text' : 'password'"
                       class="block w-full pl-10 pr-12 py-3 border border-slate-200 rounded-lg text-base placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan password" required autocomplete="current-password" />
                <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg x-show="!showPassword" class="w-5 h-5 text-slate-400 hover:text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <svg x-show="showPassword" x-cloak class="w-5 h-5 text-slate-400 hover:text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('form.password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Masuk -->
        <button type="submit" wire:loading.attr="disabled" wire:target="login"
                class="w-full mt-6 py-3 bg-blue-800 hover:bg-blue-900 text-white font-medium text-base rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="login">Masuk ke Dashboard</span>
            <span wire:loading wire:target="login">Memproses...</span>
        </button>

        <!-- Link Daftar -->
        <p class="mt-4 text-center text-sm text-slate-500">
            Belum punya akun?
            <a href="{{ route('register') }}" wire:navigate class="text-blue-700 hover:text-blue-900 font-medium">
                Daftar sekarang
            </a>
        </p>
    </form>
</div>
