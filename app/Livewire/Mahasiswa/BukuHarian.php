<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use App\Models\BukuHarian as BukuHarianModel;
use App\Models\Alert;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BukuHarian extends Component
{
    public string $newEntry = '';
    public bool $isAnalyzing = false;
    public ?array $latestAiReply = null;

    public function simpan(): void
    {
        $this->validate([
            'newEntry' => 'required|string|min:3|max:5000',
        ]);

        // Simpan entri ke DB
        $entry = BukuHarianModel::create([
            'user_id'     => Auth::id(),
            'isi'         => $this->newEntry,
            'is_analyzed' => false,
        ]);

        $this->isAnalyzing = true;

        // Coba panggil FastAPI untuk analisis
        try {
            $baseUrl = config('services.fastapi.url', 'http://127.0.0.1:8000');
            $response = Http::timeout(30)->post("{$baseUrl}/predict", [
                'text' => $this->newEntry,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $entry->update([
                    'ai_reply'    => $result['ai_reply'] ?? null,
                    'ai_saran'    => $result['ai_saran'] ?? [],
                    'label'       => $result['label'] ?? 0,
                    'risk_level'  => $result['risk_level'] ?? 'LOW',
                    'confidence'  => $result['confidence'] ?? 0,
                    'is_analyzed' => true,
                ]);

                $this->latestAiReply = [
                    'text'  => $result['ai_reply'] ?? 'Terima kasih sudah berbagi. Kami di sini untukmu.',
                    'saran' => $result['ai_saran'] ?? [],
                ];

                // Jika label >= 3 → buat Alert
                $label = $result['label'] ?? 0;
                if ($label >= 3) {
                    $alert = Alert::create([
                        'user_id'       => Auth::id(),
                        'sumber'        => 'buku_harian',
                        'sumber_id'     => $entry->id,
                        'label'         => $label,
                        'risk_level'    => $result['risk_level'] ?? 'HIGH',
                        'confidence'    => $result['confidence'] ?? 0,
                        'kata_kunci'    => $result['kata_kunci'] ?? ['curhatan berisiko'],
                        'cuplikan_teks' => mb_substr($this->newEntry, 0, 200),
                        'is_handled'    => false,
                    ]);

                    ActivityLog::create([
                        'aksi'           => 'alert_dibuat',
                        'severity'       => $label == 4 ? 'kritis' : 'waspada',
                        'alert_id'       => $alert->id,
                        'target_user_id' => Auth::id(),
                        'actor_label'    => 'Sistem',
                        'detail'         => 'Alert otomatis: analisis AI mendeteksi risiko dari buku harian',
                    ]);
                }
            } else {
                // Fallback jika FastAPI tidak tersedia
                $entry->update([
                    'ai_reply'    => 'Terima kasih sudah berbagi perasaanmu hari ini. Kamu tidak sendirian — selalu ada yang peduli padamu. 💙',
                    'ai_saran'    => ['Jalan-jalan santai di luar rumah selama 15 menit', 'Tulis 3 hal yang kamu syukuri hari ini', 'Dengarkan musik favoritmu'],
                    'label'       => 0,
                    'risk_level'  => 'LOW',
                    'confidence'  => 0,
                    'is_analyzed' => true,
                ]);

                $this->latestAiReply = [
                    'text'  => 'Terima kasih sudah berbagi perasaanmu hari ini. Kamu tidak sendirian — selalu ada yang peduli padamu. 💙',
                    'saran' => ['Jalan-jalan santai di luar rumah selama 15 menit', 'Tulis 3 hal yang kamu syukuri hari ini', 'Dengarkan musik favoritmu'],
                ];
            }
        } catch (\Exception $e) {
            // Fallback jika FastAPI tidak tersedia
            $entry->update([
                'ai_reply'    => 'Terima kasih sudah berbagi perasaanmu hari ini. Kamu tidak sendirian — selalu ada yang peduli padamu. 💙',
                'ai_saran'    => ['Jalan-jalan santai di luar rumah selama 15 menit', 'Tulis 3 hal yang kamu syukuri hari ini', 'Dengarkan musik favoritmu'],
                'label'       => 0,
                'risk_level'  => 'LOW',
                'confidence'  => 0,
                'is_analyzed' => true,
            ]);

            $this->latestAiReply = [
                'text'  => 'Terima kasih sudah berbagi perasaanmu hari ini. Kamu tidak sendirian — selalu ada yang peduli padamu. 💙',
                'saran' => ['Jalan-jalan santai di luar rumah selama 15 menit', 'Tulis 3 hal yang kamu syukuri hari ini', 'Dengarkan musik favoritmu'],
            ];
        }

        $this->isAnalyzing = false;
        $this->newEntry = '';
    }

    public function render()
    {
        $entries = BukuHarianModel::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.mahasiswa.buku-harian', [
            'entries' => $entries,
        ]);
    }
}
