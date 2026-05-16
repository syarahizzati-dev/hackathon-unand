<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.fastapi.url', 'http://127.0.0.1:8000');
        $this->timeout = 30;
    }

    /**
     * Prediksi label risiko dari teks menggunakan IndoBERT via FastAPI.
     *
     * @return array{label: int, risk_level: string, confidence: float, ai_reply: string, ai_saran: array}
     */
    public function predict(string $text): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/predict", [
                    'text' => $text,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $label = (int) ($data['label'] ?? 0);
                $local = $this->localAnalysis($text);

                if ($local['label'] > $label) {
                    $label = $local['label'];
                }

                return [
                    'label'      => $label,
                    'risk_level' => $this->riskLevelFromLabel($label),
                    'confidence' => max((float) ($data['confidence'] ?? 0.0), $local['confidence']),
                    'ai_reply'   => $data['ai_reply'] ?? $this->fallbackReply($label),
                    'ai_saran'   => $this->suggestionsForText($text, $label, $data['ai_saran'] ?? []),
                ];
            }

            Log::warning('AIService: FastAPI returned non-200', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return $this->fallbackResponse($text);

        } catch (\Exception $e) {
            Log::warning('AIService: Gagal menghubungi FastAPI', [
                'error' => $e->getMessage(),
            ]);

            return $this->fallbackResponse($text);
        }
    }

    /**
     * Proses teks, prediksi, dan otomatis buat Alert jika label >= 3.
     *
     * @return array{label: int, risk_level: string, confidence: float, ai_reply: string, ai_saran: array, alert_created: bool}
     */
    public static function processAndAlert(string $text, User $user, string $sumber, int $sumberId): array
    {
        $service = new self();
        $result = $service->predict($text);
        $result['alert_created'] = false;

        if ($result['label'] >= 3) {
            $alert = Alert::create([
                'user_id'       => $user->id,
                'sumber'        => $sumber,
                'sumber_id'     => $sumberId,
                'label'         => $result['label'],
                'risk_level'    => $result['risk_level'],
                'confidence'    => $result['confidence'],
                'kata_kunci'    => self::extractKeywords($text),
                'cuplikan_teks' => mb_substr($text, 0, 200),
                'is_handled'    => false,
            ]);

            ActivityLog::create([
                'aksi'           => 'alert_dibuat',
                'severity'       => $result['label'] >= 4 ? 'kritis' : 'waspada',
                'alert_id'       => $alert->id,
                'target_user_id' => $user->id,
                'actor_label'    => 'Sistem',
                'detail'         => 'Alert otomatis: analisis AI mendeteksi risiko (' . $result['risk_level'] . ', confidence: ' . round($result['confidence'] * 100) . '%)',
            ]);

            $result['alert_created'] = true;
        }

        return $result;
    }

    /**
     * Health check endpoint FastAPI.
     */
    public function healthCheck(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    // ─── Fallback Methods ────────────────────────────────────

    protected function fallbackResponse(string $text = ''): array
    {
        $local = $this->localAnalysis($text);
        $label = $local['label'];

        return [
            'label'      => $label,
            'risk_level' => $this->riskLevelFromLabel($label),
            'confidence' => $local['confidence'],
            'ai_reply'   => $this->fallbackReply($label),
            'ai_saran'   => $this->suggestionsForText($text, $label),
        ];
    }

    protected function fallbackReply(int $label): string
    {
        return match (true) {
            $label >= 4 => 'Kami sangat peduli dengan kondisimu. Kamu tidak sendirian — tolong hubungi orang terdekatmu atau konselor kampus sekarang. 💙',
            $label >= 3 => 'Terima kasih sudah mau bercerita. Perasaanmu valid dan penting. Pertimbangkan untuk berbicara dengan seseorang yang kamu percaya. 💙',
            $label >= 2 => 'Terima kasih sudah berbagi. Kamu sedang melewati masa yang tidak mudah, tapi kamu kuat. Jangan ragu untuk mencari dukungan. 💙',
            default     => 'Terima kasih sudah berbagi perasaanmu hari ini. Kamu tidak sendirian — selalu ada yang peduli padamu. 💙',
        };
    }

    protected function fallbackSaran(int $label): array
    {
        return match (true) {
            $label >= 3 => [
                'Hubungi konselor kampus atau saluran kesehatan mental terdekat',
                'Ceritakan perasaanmu ke orang yang kamu percaya',
                'Lakukan aktivitas grounding: tarik napas dalam 4-7-8',
            ],
            $label >= 2 => [
                'Luangkan waktu untuk istirahat dan relaksasi',
                'Coba teknik pernapasan untuk menenangkan diri',
                'Tulis 3 hal yang kamu syukuri hari ini',
            ],
            default => [
                'Jalan-jalan santai di luar rumah selama 15 menit',
                'Tulis 3 hal yang kamu syukuri hari ini',
                'Dengarkan musik favoritmu',
            ],
        };
    }

    protected function localAnalysis(string $text): array
    {
        $textLower = mb_strtolower($text);
        $criticalWords = [
            'bunuh diri', 'ingin mati', 'akhiri hidup', 'mengakhiri hidup',
            'tidak ingin hidup', 'lebih baik mati', 'menyakiti diri',
        ];
        $highWords = [
            'tidak berguna', 'putus asa', 'tidak ada harapan', 'tidak ada gunanya',
            'tidak ada yang peduli', 'depresi', 'tertekan', 'stress berat', 'stres berat',
        ];
        $mediumWords = [
            'cemas', 'khawatir', 'panik', 'sendiri', 'kesepian', 'menangis',
            'tidak bisa tidur', 'lelah', 'capek', 'overthinking',
        ];

        foreach ($criticalWords as $word) {
            if (str_contains($textLower, $word)) {
                return ['label' => 4, 'confidence' => 0.95];
            }
        }

        foreach ($highWords as $word) {
            if (str_contains($textLower, $word)) {
                return ['label' => 3, 'confidence' => 0.9];
            }
        }

        foreach ($mediumWords as $word) {
            if (str_contains($textLower, $word)) {
                return ['label' => 2, 'confidence' => 0.85];
            }
        }

        return ['label' => 0, 'confidence' => 0.0];
    }

    protected function riskLevelFromLabel(int $label): string
    {
        return match (true) {
            $label >= 4 => 'CRITICAL',
            $label >= 3 => 'HIGH',
            $label >= 2 => 'MEDIUM',
            default => 'LOW',
        };
    }

    protected function suggestionsForText(string $text, int $label, array $apiSuggestions = []): array
    {
        $textLower = mb_strtolower($text);
        $suggestions = [];

        if (str_contains($textLower, 'tidur') || str_contains($textLower, 'lelah') || str_contains($textLower, 'capek')) {
            $suggestions[] = 'Matikan layar 30 menit sebelum tidur dan usahakan tidur lebih awal malam ini';
            $suggestions[] = 'Lakukan peregangan ringan 10 menit untuk melepas tegang';
        }

        if (str_contains($textLower, 'cemas') || str_contains($textLower, 'khawatir') || str_contains($textLower, 'panik') || str_contains($textLower, 'overthinking')) {
            $suggestions[] = 'Coba teknik napas 4-7-8 selama 3 putaran';
            $suggestions[] = 'Lakukan grounding 5-4-3-2-1 untuk menenangkan pikiran';
        }

        if (str_contains($textLower, 'sendiri') || str_contains($textLower, 'kesepian') || str_contains($textLower, 'menangis')) {
            $suggestions[] = 'Kirim pesan ke satu teman atau keluarga yang kamu percaya';
            $suggestions[] = 'Tulis perasaanmu selama 10 menit tanpa menilai diri sendiri';
        }

        if ($label >= 4) {
            array_unshift($suggestions, 'Segera hubungi konselor kampus, kontak darurat, atau hotline kesehatan mental 119 ext. 8');
        } elseif ($label >= 3) {
            array_unshift($suggestions, 'Jadwalkan sesi dengan konselor kampus secepatnya');
        }

        $suggestions = array_values(array_unique(array_merge($suggestions, $apiSuggestions, $this->fallbackSaran($label))));

        return array_slice($suggestions, 0, 3);
    }

    /**
     * Ekstrak kata kunci berbahaya dari teks (public alias).
     */
    public static function extractKeywordsStatic(string $text): array
    {
        return self::extractKeywords($text);
    }

    /**
     * Ekstrak kata kunci berbahaya dari teks.
     */
    protected static function extractKeywords(string $text): array
    {
        $keywords = [];
        $dangerWords = [
            'bunuh diri', 'mati', 'tidak ingin hidup', 'menyakiti diri',
            'tidak berguna', 'putus asa', 'tidak ada harapan', 'akhiri hidup',
            'tidak ada gunanya', 'ingin mati', 'lebih baik mati',
            'tidak ada yang peduli', 'sendiri', 'depresi', 'cemas',
            'tertekan', 'stress berat', 'tidak bisa tidur', 'menangis',
        ];

        $textLower = mb_strtolower($text);
        foreach ($dangerWords as $word) {
            if (str_contains($textLower, $word)) {
                $keywords[] = $word;
            }
        }

        return array_slice($keywords, 0, 5);
    }
}
