<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\SelfCheck;
use App\Models\BukuHarian;
use App\Models\ForumPost;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public int $totalMahasiswa = 0;
    public int $zonaAman = 0;
    public int $waspada = 0;
    public int $kritis = 0;
    public float $zonaAmanPct = 0;
    public float $waspadaPct = 0;
    public float $kritisPct = 0;

    // Tren Minggu Ini
    public int $selfCheckCount = 0;
    public int $selfCheckPrev = 0;
    public int $diaryCount = 0;
    public int $diaryPrev = 0;
    public int $forumCount = 0;
    public int $forumPrev = 0;

    // Jurusan terbanyak
    public array $topJurusan = [];

    public function mount(): void
    {
        $this->totalMahasiswa = User::where('is_admin', false)->count();

        if ($this->totalMahasiswa > 0) {
            // Subquery: ambil self_check terbaru per user
            $latestChecks = SelfCheck::select('user_id', DB::raw('MAX(id) as latest_id'))
                ->groupBy('user_id');

            $latestData = SelfCheck::joinSub($latestChecks, 'latest', function ($join) {
                $join->on('self_checks.id', '=', 'latest.latest_id');
            })->get();

            $this->zonaAman = $latestData->filter(fn($c) => $c->label <= 1)->count();
            $this->waspada = $latestData->filter(fn($c) => $c->label == 2)->count();
            $this->kritis = $latestData->filter(fn($c) => $c->label >= 3)->count();

            // Mahasiswa tanpa self-check = zona aman (belum terdeteksi)
            $checkedUsers = $latestData->count();
            $uncheckedUsers = $this->totalMahasiswa - $checkedUsers;
            $this->zonaAman += $uncheckedUsers;

            $this->zonaAmanPct = round(($this->zonaAman / $this->totalMahasiswa) * 100, 1);
            $this->waspadaPct = round(($this->waspada / $this->totalMahasiswa) * 100, 1);
            $this->kritisPct = round(($this->kritis / $this->totalMahasiswa) * 100, 1);
        }

        // Tren minggu ini vs minggu lalu
        $thisWeekStart = now()->startOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();

        $this->selfCheckCount = SelfCheck::where('created_at', '>=', $thisWeekStart)->count();
        $this->selfCheckPrev = SelfCheck::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();

        $this->diaryCount = BukuHarian::where('created_at', '>=', $thisWeekStart)->count();
        $this->diaryPrev = BukuHarian::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();

        $this->forumCount = ForumPost::where('created_at', '>=', $thisWeekStart)->count();
        $this->forumPrev = ForumPost::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();

        // Top jurusan
        $this->topJurusan = User::where('is_admin', false)
            ->whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->select('jurusan', DB::raw('COUNT(*) as total'))
            ->groupBy('jurusan')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($j) => ['nama' => $j->jurusan, 'total' => $j->total])
            ->toArray();
    }

    public function getTrendPercentage(int $current, int $previous): array
    {
        if ($previous === 0) {
            return ['value' => $current > 0 ? 100 : 0, 'direction' => 'up'];
        }
        $pct = round((($current - $previous) / $previous) * 100);
        return ['value' => abs($pct), 'direction' => $pct >= 0 ? 'up' : 'down'];
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'selfCheckTrend' => $this->getTrendPercentage($this->selfCheckCount, $this->selfCheckPrev),
            'diaryTrend' => $this->getTrendPercentage($this->diaryCount, $this->diaryPrev),
            'forumTrend' => $this->getTrendPercentage($this->forumCount, $this->forumPrev),
        ]);
    }
}
