<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SelfCheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MoodTracker extends Component
{
    public int $currentMonth;
    public int $currentYear;
    public array $moodData = [];

    public function mount(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->loadMoodData();
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadMoodData();
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadMoodData();
    }

    public function loadMoodData(): void
    {
        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Load self-check terbaru per hari
        $checks = SelfCheck::where('user_id', Auth::id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($check) => $check->created_at->format('Y-m-d'));

        $this->moodData = [];
        foreach ($checks as $date => $dayChecks) {
            $latestCheck = $dayChecks->first();
            $riskLevel = strtoupper($latestCheck->risk_level ?? 'LOW');
            $mood = $this->moodFromRiskLevel($riskLevel);

            $this->moodData[$date] = [
                'skor' => $latestCheck->skor_total,
                'mood' => $mood,
                'risk_level' => $riskLevel,
                'label' => $latestCheck->label,
                'confidence' => $latestCheck->confidence,
                'created_at' => $latestCheck->created_at->format('H.i'),
                'description' => $this->descriptionFromRiskLevel($riskLevel),
            ];
        }
    }

    #[On('self-check-completed')]
    public function refreshAfterSelfCheck(): void
    {
        $this->loadMoodData();
    }

    public function getCalendarProperty(): array
    {
        $firstDay = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDayOfWeek = $firstDay->dayOfWeek; // 0=Sunday

        $calendar = [];

        // Pad leading blanks for days before the 1st
        for ($i = 0; $i < $startDayOfWeek; $i++) {
            $calendar[] = null;
        }

        // Fill in actual days
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = Carbon::create($this->currentYear, $this->currentMonth, $day)->format('Y-m-d');
            $calendar[] = [
                'day' => $day,
                'date' => $dateStr,
                'mood' => $this->moodData[$dateStr] ?? null,
                'isToday' => $dateStr === now()->format('Y-m-d'),
            ];
        }

        return $calendar;
    }

    public function getMonthNameProperty(): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $bulan[$this->currentMonth] . ' ' . $this->currentYear;
    }

    public function getStatisticsProperty(): array
    {
        $baik = 0;
        $waspada = 0;
        $kritis = 0;

        foreach ($this->moodData as $data) {
            match ($data['mood']) {
                'baik' => $baik++,
                'waspada' => $waspada++,
                'kritis' => $kritis++,
                default => null,
            };
        }

        return ['baik' => $baik, 'waspada' => $waspada, 'kritis' => $kritis];
    }

    private function moodFromRiskLevel(string $riskLevel): string
    {
        return match ($riskLevel) {
            'CRITICAL' => 'kritis',
            'MEDIUM', 'HIGH' => 'waspada',
            default => 'baik',
        };
    }

    private function descriptionFromRiskLevel(string $riskLevel): string
    {
        return match ($riskLevel) {
            'CRITICAL' => 'Risiko CRITICAL, perlu bantuan segera',
            'HIGH' => 'Risiko HIGH, perlu perhatian dan dukungan',
            'MEDIUM' => 'Risiko MEDIUM, perlu pemantauan',
            default => 'Risiko LOW, kondisi stabil',
        };
    }

    public function render()
    {
        return view('livewire.mahasiswa.mood-tracker', [
            'calendar' => $this->calendar,
            'monthName' => $this->monthName,
            'statistics' => $this->statistics,
        ]);
    }
}
