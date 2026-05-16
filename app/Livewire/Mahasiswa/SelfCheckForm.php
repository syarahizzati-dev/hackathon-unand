<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use App\Models\SelfCheck;
use App\Models\Alert;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class SelfCheckForm extends Component
{
    public int $currentQuestion = 1;
    public array $jawaban = [];
    public bool $isComplete = false;
    public ?int $skorTotal = null;

    /** @var array<int, array{teks: string}> */
    public array $pertanyaan = [
        1 => ['teks' => 'Seberapa baik kamu tidur semalam?'],
        2 => ['teks' => 'Bagaimana mood kamu hari ini?'],
        3 => ['teks' => 'Apakah kamu merasa cemas atau khawatir?'],
        4 => ['teks' => 'Seberapa termotivasi kamu untuk melakukan aktivitas?'],
        5 => ['teks' => 'Apakah kamu merasa sendirian atau terisolasi?'],
    ];

    /** @var array<int, array{label: string, value: int, color: string, bg: string}> */
    public array $opsiJawaban = [
        ['label' => 'Sangat Baik', 'value' => 5, 'color' => 'text-white', 'bg' => 'bg-emerald-600 hover:bg-emerald-700'],
        ['label' => 'Baik',        'value' => 4, 'color' => 'text-white', 'bg' => 'bg-green-500 hover:bg-green-600'],
        ['label' => 'Biasa',       'value' => 3, 'color' => 'text-white', 'bg' => 'bg-yellow-500 hover:bg-yellow-600'],
        ['label' => 'Kurang',      'value' => 2, 'color' => 'text-white', 'bg' => 'bg-orange-500 hover:bg-orange-600'],
        ['label' => 'Buruk',       'value' => 1, 'color' => 'text-white', 'bg' => 'bg-red-500 hover:bg-red-600'],
    ];

    public function selectAnswer(int $question, int $value): void
    {
        $this->jawaban[$question] = $value;

        if ($question < 5) {
            $this->currentQuestion = $question + 1;
        } else {
            $this->submit();
        }
    }

    public function submit(): void
    {
        $this->skorTotal = array_sum($this->jawaban);

        // Buat teks gabungan untuk analisis AI
        $labelMap = [5 => 'Sangat Baik', 4 => 'Baik', 3 => 'Biasa', 2 => 'Kurang', 1 => 'Buruk'];
        $teksGabung = '';
        foreach ($this->pertanyaan as $num => $q) {
            $val = $this->jawaban[$num] ?? 3;
            $teksGabung .= $q['teks'] . ': ' . ($labelMap[$val] ?? 'Biasa') . '. ';
        }

        // Tentukan label dan risk_level berdasarkan skor
        // Skor range: 5-25
        // 20-25 = label 0-1 (Normal/Low), 13-19 = label 2 (Medium), 5-12 = label 3-4 (High/Critical)
        if ($this->skorTotal >= 20) {
            $label = 0;
            $riskLevel = 'LOW';
        } elseif ($this->skorTotal >= 15) {
            $label = 1;
            $riskLevel = 'LOW';
        } elseif ($this->skorTotal >= 10) {
            $label = 2;
            $riskLevel = 'MEDIUM';
        } elseif ($this->skorTotal >= 7) {
            $label = 3;
            $riskLevel = 'HIGH';
        } else {
            $label = 4;
            $riskLevel = 'CRITICAL';
        }

        // Simpan ke database
        $selfCheck = SelfCheck::create([
            'user_id'    => Auth::id(),
            'jawaban_1'  => $this->jawaban[1] ?? 3,
            'jawaban_2'  => $this->jawaban[2] ?? 3,
            'jawaban_3'  => $this->jawaban[3] ?? 3,
            'jawaban_4'  => $this->jawaban[4] ?? 3,
            'jawaban_5'  => $this->jawaban[5] ?? 3,
            'skor_total' => $this->skorTotal,
            'teks_gabung' => $teksGabung,
            'label'      => $label,
            'risk_level' => $riskLevel,
            'confidence' => 0.85,
        ]);

        // Jika label >= 3 → buat Alert
        if ($label >= 3) {
            $alert = Alert::create([
                'user_id'       => Auth::id(),
                'sumber'        => 'self_check',
                'sumber_id'     => $selfCheck->id,
                'label'         => $label,
                'risk_level'    => $riskLevel,
                'confidence'    => 0.85,
                'kata_kunci'    => ['self-check kritis'],
                'cuplikan_teks' => mb_substr($teksGabung, 0, 200),
                'is_handled'    => false,
            ]);

            ActivityLog::create([
                'aksi'           => 'alert_dibuat',
                'severity'       => $label == 4 ? 'kritis' : 'waspada',
                'alert_id'       => $alert->id,
                'target_user_id' => Auth::id(),
                'actor_label'    => 'Sistem',
                'detail'         => 'Alert otomatis: skor self-check rendah (' . $this->skorTotal . '/25)',
            ]);
        }

        $this->isComplete = true;
    }

    public function render()
    {
        return view('livewire.mahasiswa.self-check-form');
    }
}
