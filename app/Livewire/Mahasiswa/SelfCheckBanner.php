<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class SelfCheckBanner extends Component
{
    public bool $showBanner = true;
    public bool $showForm = false;

    public function mount(): void
    {
        // Cek apakah hari ini sudah self-check
        $today = now()->toDateString();
        $alreadyChecked = Auth::user()
            ->selfChecks()
            ->whereDate('created_at', $today)
            ->exists();

        $this->showBanner = !$alreadyChecked;
    }

    public function dismiss(): void
    {
        $this->showBanner = false;
    }

    public function mulai(): void
    {
        $this->showForm = true;
        $this->showBanner = false;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.mahasiswa.self-check-banner');
    }
}
