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

                return [
                    'label'      => $data['label'] ?? 0,
                    'risk_level' => $data['risk_level'] ?? 'LOW',
                    'confidence' => $data['confidence'] ?? 0.0,
                    'ai_reply'   => $data['ai_reply'] ?? $this->fallbackReply($data['label'] ?? 0),
                    'ai_saran'   => $data['ai_saran'] ?? $this->fallbackSaran($data['label'] ?? 0),
                ];
            }

            Log::warning('AIService: FastAPI returned non-200', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return $this->fallbackResponse();

        } catch (\Exception $e) {
            Log::warning('AIService: Gagal menghubungi FastAPI', [
                'error' => $e->getMessage(),
            ]);

            return $this->fallbackResponse();
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

    protected function fallbackResponse(): array
    {
        return [
            'label'      => 0,
            'risk_level' => 'LOW',
            'confidence' => 0.0,
            'ai_reply'   => $this->fallbackReply(0),
            'ai_saran'   => $this->fallbackSaran(0),
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
