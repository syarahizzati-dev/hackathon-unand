<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use App\Models\SelfCheck;
use App\Models\Alert;
use App\Models\ActivityLog;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;

class SelfCheckForm extends Component
{
    public int $currentQuestion = 1;
    public array $jawaban = [];
    public bool $isComplete = false;
    public bool $readyToSubmit = false;
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
        ['label' => 'Sangat Baik', 'value' => 5, 'color' => 'text-white', 'bg' => 'bg-[#00c950] hover:bg-[#00b046]'],
        ['label' => 'Baik',        'value' => 4, 'color' => 'text-white', 'bg' => 'bg-[#05df72] hover:bg-[#04c766]'],
        ['label' => 'Biasa',       'value' => 3, 'color' => 'text-white', 'bg' => 'bg-[#fdc700] hover:bg-[#e2b200]'],
        ['label' => 'Kurang',      'value' => 2, 'color' => 'text-white', 'bg' => 'bg-[#ff8904] hover:bg-[#e57a03]'],
        ['label' => 'Buruk',       'value' => 1, 'color' => 'text-white', 'bg' => 'bg-[#fb2c36] hover:bg-[#e02730]'],
    ];

    public function selectAnswer(int $question, int $value): void
    {
        $this->jawaban[$question] = $value;

        if ($question < count($this->pertanyaan)) {
            $this->currentQuestion = $question + 1;
        } else {
            $this->readyToSubmit = true;
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

        // ─── Prediksi via AIService (IndoBERT) ─────────────────
        $service = new AIService();
        $result = $service->predict($teksGabung);

        $aiLabel = $result['label'] ?? 0;
        $scoreLabel = $this->labelFromScore($this->skorTotal);
        $label = $scoreLabel;
        $riskLevel = $this->riskLevelFromLabel($label);
        $confidence = max((float) ($result['confidence'] ?? 0), 0.85);

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
            'confidence' => $confidence,
        ]);

        // Jika label >= 3 → buat Alert
        if ($label >= 3) {
            $moodStatus = $riskLevel === 'CRITICAL' ? 'Kritis' : 'Waspada';

            $alert = Alert::create([
                'user_id'       => Auth::id(),
                'sumber'        => 'self_check',
                'sumber_id'     => $selfCheck->id,
                'label'         => $label,
                'risk_level'    => $riskLevel,
                'confidence'    => $confidence,
                'kata_kunci'    => ['self-check kritis', 'skor rendah: ' . $this->skorTotal . '/25'],
                'cuplikan_teks' => mb_substr($teksGabung, 0, 200),
                'admin_steps' => $service->buildSafeOutput($teksGabung, $label)['admin_steps'],
                'analysis_metadata' => [
                    'ai_label' => $aiLabel,
                    'score_label' => $scoreLabel,
                    'final_label' => $label,
                    'final_risk_level' => $riskLevel,
                    'score_total' => $this->skorTotal,
                ],
                'is_handled'    => false,
            ]);

            ActivityLog::create([
                'aksi'           => 'alert_dibuat',
                'severity'       => $label >= 4 ? 'kritis' : 'waspada',
                'alert_id'       => $alert->id,
                'target_user_id' => Auth::id(),
                'actor_label'    => 'Sistem',
                'detail'         => 'Alert otomatis: self-check ' . $moodStatus . ' (skor ' . $this->skorTotal . '/25, risiko: ' . $riskLevel . ')',
            ]);
        }

        $this->dispatch('self-check-completed');
        $this->isComplete = true;
        $this->readyToSubmit = false;
    }

    public function render()
    {
        return view('livewire.mahasiswa.self-check-form');
    }

    private function labelFromScore(int $score): int
    {
        return match (true) {
            $score >= 21 => 0,
            $score >= 17 => 1,
            $score >= 13 => 2,
            $score >= 9 => 3,
            default => 4,
        };
    }

    private function riskLevelFromLabel(int $label): string
    {
        return match (true) {
            $label >= 4 => 'CRITICAL',
            $label >= 3 => 'HIGH',
            $label >= 2 => 'MEDIUM',
            default => 'LOW',
        };
    }
}
