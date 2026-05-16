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
        $this->timeout = 8;
    }

    /**
     * @return array{label: int, risk_level: string, confidence: float, ai_reply: string, ai_saran: array, admin_steps: array, analysis_metadata: array}
     */
    public function predict(string $text): array
    {
        $local = $this->localAnalysis($text);
        $fastApi = $this->predictWithFastApi($text);
        $label = max($local['label'], (int) ($fastApi['label'] ?? 0));
        $confidence = max($local['confidence'], (float) ($fastApi['confidence'] ?? 0.0));
        $gemini = $this->generateWithGemini($text, $label);
        $candidateReply = $gemini['reply'] ?? null;
        $replyAccepted = is_string($candidateReply) && trim($candidateReply) !== '' && $this->replyMatchesSeverity($candidateReply, $label);

        return [
            'label' => $label,
            'risk_level' => $this->riskLevelFromLabel($label),
            'confidence' => $confidence,
            'ai_reply' => $this->safeReplyForLabel($label, $candidateReply),
            'ai_saran' => $this->suggestionsForText($text, $label, $gemini['suggestions'] ?? []),
            'admin_steps' => $this->adminStepsForRisk($label, $gemini['admin_steps'] ?? []),
            'analysis_metadata' => [
                'local_label' => $local['label'],
                'local_confidence' => $local['confidence'],
                'local_match' => $local['match'] ?? null,
                'fastapi_label' => $fastApi['label'] ?? null,
                'fastapi_confidence' => $fastApi['confidence'] ?? null,
                'final_label' => $label,
                'final_risk_level' => $this->riskLevelFromLabel($label),
                'reply_source' => $replyAccepted ? 'gemini' : 'fallback',
            ],
        ];
    }

    /**
     * @return array{label: int, risk_level: string, confidence: float, ai_reply: string, ai_saran: array, admin_steps: array, analysis_metadata?: array, alert_created: bool}
     */
    public static function processAndAlert(string $text, User $user, string $sumber, int $sumberId): array
    {
        $service = new self();
        $result = $service->predict($text);
        $result['alert_created'] = false;

        if ($result['label'] >= 3) {
            $alert = Alert::create([
                'user_id' => $user->id,
                'sumber' => $sumber,
                'sumber_id' => $sumberId,
                'label' => $result['label'],
                'risk_level' => $result['risk_level'],
                'confidence' => $result['confidence'],
                'kata_kunci' => self::extractKeywords($text),
                'cuplikan_teks' => mb_substr($text, 0, 200),
                'admin_steps' => $result['admin_steps'],
                'analysis_metadata' => $result['analysis_metadata'] ?? null,
                'is_handled' => false,
            ]);

            ActivityLog::create([
                'aksi' => 'alert_dibuat',
                'severity' => $result['label'] >= 4 ? 'kritis' : 'waspada',
                'alert_id' => $alert->id,
                'target_user_id' => $user->id,
                'actor_label' => 'Sistem',
                'detail' => 'Alert otomatis: analisis AI mendeteksi risiko (' . $result['risk_level'] . ', confidence: ' . round($result['confidence'] * 100) . '%)',
            ]);

            $result['alert_created'] = true;
        }

        return $result;
    }

    public function healthCheck(): bool
    {
        try {
            return Http::timeout(5)->get("{$this->baseUrl}/health")->successful();
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Public safety-only entry point for deterministic tests and offline checks.
     *
     * @return array{label: int, risk_level: string, confidence: float}
     */
    public function analyzeLocal(string $text): array
    {
        $result = $this->localAnalysis($text);

        return [
            'label' => $result['label'],
            'risk_level' => $this->riskLevelFromLabel($result['label']),
            'confidence' => $result['confidence'],
        ];
    }

    /**
     * Build output from final risk without calling external AI services.
     *
     * @return array{ai_reply: string, ai_saran: array, admin_steps: array}
     */
    public function buildSafeOutput(string $text, int $label, ?string $candidateReply = null, array $candidateSuggestions = []): array
    {
        return [
            'ai_reply' => $this->safeReplyForLabel($label, $candidateReply),
            'ai_saran' => $this->suggestionsForText($text, $label, $candidateSuggestions),
            'admin_steps' => $this->adminStepsForRisk($label),
        ];
    }

    protected function predictWithFastApi(string $text): array
    {
        try {
            $response = Http::timeout($this->timeout)->post("{$this->baseUrl}/predict", [
                'text' => $text,
            ]);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            Log::warning('AIService: FastAPI returned non-200', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::warning('AIService: FastAPI unavailable', ['error' => $e->getMessage()]);
        }

        return [];
    }

    protected function generateWithGemini(string $text, int $label): array
    {
        $key = config('services.gemini.key');
        if (!$key) {
            return [];
        }

        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $baseUrl = rtrim(config('services.gemini.url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $riskLevel = $this->riskLevelFromLabel($label);

        $prompt = <<<PROMPT
Kamu adalah asisten kesehatan mental kampus. Balas dalam Bahasa Indonesia, ringkas, empatik, aman, dan tidak menghakimi.
Risk final dari sistem: {$riskLevel}. Jangan menurunkan tingkat risiko.
Teks mahasiswa: {$text}

Kembalikan JSON valid saja dengan schema:
{"reply":"string","suggestions":["saran 1","saran 2","saran 3"],"admin_steps":["langkah admin 1","langkah admin 2","langkah admin 3","langkah admin 4"]}
Untuk CRITICAL, reply wajib menyarankan tidak sendirian dan segera hubungi konselor/kontak darurat/layanan darurat.
PROMPT;

        try {
            $response = Http::timeout($this->timeout)->post("{$baseUrl}/models/{$model}:generateContent?key={$key}", [
                'contents' => [[
                    'parts' => [['text' => $prompt]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if (!$response->successful()) {
                Log::warning('AIService: Gemini returned non-200', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $textResponse = data_get($response->json(), 'candidates.0.content.parts.0.text');
            $decoded = is_string($textResponse) ? json_decode($textResponse, true) : null;

            return is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            Log::warning('AIService: Gemini unavailable', ['error' => $e->getMessage()]);
            return [];
        }
    }

    protected function fallbackReply(int $label): string
    {
        return match (true) {
            $label >= 4 => $this->criticalReply(),
            $label >= 3 => 'Terima kasih sudah mau bercerita. Perasaanmu valid dan penting. Coba hubungi orang yang kamu percaya atau konselor kampus agar kamu tidak memikul ini sendirian.',
            $label >= 2 => 'Terima kasih sudah berbagi. Kamu sedang melewati hal yang berat. Ambil jeda, tenangkan napas, dan cari dukungan dari orang yang kamu percaya.',
            default => 'Terima kasih sudah berbagi perasaanmu hari ini. Tetap jaga diri dan lakukan hal kecil yang membuatmu merasa lebih nyaman.',
        };
    }

    protected function safeReplyForLabel(int $label, ?string $candidate): string
    {
        $candidate = trim((string) $candidate);

        if ($candidate !== '' && $this->replyMatchesSeverity($candidate, $label)) {
            return $candidate;
        }

        return $this->fallbackReply($label);
    }

    protected function replyMatchesSeverity(string $reply, int $label): bool
    {
        $normalized = $this->normalizeText($reply);

        if ($label >= 4) {
            $urgencyMarkers = [
                'segera', 'sekarang', 'saat ini', 'darurat', 'dalam bahaya', 'jangan sendirian',
            ];
            $helpMarkers = [
                'konselor', 'kontak darurat', 'layanan darurat', 'orang terdekat', 'teman', 'keluarga', 'bantuan',
            ];

            return $this->containsAny($normalized, $urgencyMarkers)
                && $this->containsAny($normalized, $helpMarkers);
        }

        if ($label >= 3) {
            return $this->containsAny($normalized, [
                'dukungan', 'konselor', 'orang yang kamu percaya', 'bantuan',
                'tidak sendirian', 'jangan memikul ini sendirian',
            ]);
        }

        return true;
    }

    protected function criticalReply(): string
    {
        return 'Aku sangat khawatir membaca ini, dan terima kasih karena kamu sudah berani menuliskannya. Kamu tidak harus menghadapi ini sendirian. Tolong segera hubungi orang terdekat, konselor kampus, kontak darurat, atau layanan darurat. Jika kamu sedang dalam bahaya sekarang, jauhi benda yang bisa membahayakan diri dan cari orang yang bisa menemanimu saat ini.';
    }

    protected function fallbackSaran(int $label): array
    {
        return match (true) {
            $label >= 4 => [
                'Jangan sendirian sekarang; hubungi teman, keluarga, konselor kampus, atau kontak darurat',
                'Jauhkan benda yang bisa membahayakan diri dari sekitarmu',
                'Pindah ke tempat yang lebih aman dan cari orang yang bisa menemanimu',
                'Hubungi layanan darurat atau hotline kesehatan mental 119 ext. 8 jika risiko terasa mendesak',
                'Tetap terhubung dengan seseorang sampai bantuan datang atau kondisimu lebih aman',
            ],
            $label >= 3 => [
                'Ceritakan kondisi ini ke satu orang yang kamu percaya hari ini',
                'Jadwalkan konsultasi dengan konselor kampus secepatnya',
                'Lakukan grounding 5-4-3-2-1 saat pikiran terasa penuh',
            ],
            $label >= 2 => [
                'Coba teknik napas 4-7-8 selama 3 putaran',
                'Tulis hal yang membuatmu cemas lalu pisahkan mana yang bisa dikendalikan',
                'Ambil jeda 10 menit dari tugas atau layar',
            ],
            default => [
                'Jalan santai di luar ruangan selama 15 menit',
                'Tulis 3 hal yang kamu syukuri hari ini',
                'Tidur cukup dan minum air putih',
            ],
        };
    }

    protected function localAnalysis(string $text): array
    {
        $normalized = $this->normalizeText($text);

        $safeContextPatterns = [
            '/\bmain lompat tali\b/u',
            '/\blompat (jauh|tinggi)\b/u',
            '/\bterjun payung\b/u',
            '/\bterjun ke kolam\b/u',
            '/\b(hp|handphone|ponsel|laptop|komputer|wifi|lampu|mesin|motor|mobil) mati\b/u',
            '/\bmati gaya\b/u',
            '/\bminum obat (dokter|resep)\b/u',
            '/\bobat (dokter|resep)\b/u',
            '/\bend (meeting|kelas|semester|sesi|zoom|call)\b/u',
            '/\blogout (akun|aplikasi|email|instagram|ig|whatsapp|wa)\b/u',
            '/\bgame over main\b/u',
            '/\bngilangin (file|data|foto|chat|pesan)\b/u',
        ];

        $hasSafeContext = $this->matchesAnyPattern($normalized, $safeContextPatterns);

        $criticalPhrases = [
            'mau bunuh diri', 'ingin bunuh diri', 'pengen bunuh diri', 'kepikiran bunuh diri', 'bunuh diri',
            'mau mati', 'ingin mati', 'pengen mati', 'rasanya mau mati', 'lebih baik mati',
            'akhiri hidup', 'mengakhiri hidup', 'mengakhiri semuanya', 'tidak ingin hidup', 'tidak mau hidup',
            'gak mau hidup', 'ga mau hidup', 'nggak mau hidup', 'tidak pengen hidup', 'gak pengen hidup',
            'menyakiti diri', 'nyakitin diri', 'melukai diri', 'self harm', 'suicide',
            'logout dari dunia', 'log out dari dunia', 'logout dari bumi', 'game over dari dunia',
            'ingin mengakhiri hidup', 'end it all', 'pengen ngilang selamanya', 'pengen hilang selamanya',
            'pengen ngilang dari bumi', 'pengen hilang dari bumi', 'gantung diri', 'bye dunia',
            'selamat tinggal dunia', 'pamitan dari dunia', 'cabut dari dunia',
        ];
        $criticalMethodPatterns = [
            '/\b(mau|ingin|pengen|kepikiran|rasanya mau|rasanya pengen)\s+(lompat|loncat|terjun)\s+dari\s+(gedung|jembatan|lantai|atap|balkon|ketinggian)\b/u',
            '/\b(mau|ingin|pengen|kepikiran)\s+(minum|makan)\s+(racun|obat banyak|banyak obat)\b/u',
            '/\b(mau|ingin|pengen|kepikiran)\s+(overdosis|od)\b/u',
            '/\b(mau|ingin|pengen|kepikiran)\s+(nyilet|nyayat|cutting|gantung diri)\b/u',
            '/\b(mau|ingin|pengen|kepikiran)\s+(tidur selamanya|tidur dan tidak bangun lagi)\b/u',
            '/\bsemoga\s+(tidak|gak|ga|nggak)\s+bangun lagi\b/u',
            '/\b(pengen|ingin|mau)\s+(tidak|gak|ga|nggak)\s+bangun lagi\b/u',
        ];
        $highPhrases = [
            'tidak kuat lagi', 'ga kuat lagi', 'gak kuat lagi', 'nggak kuat lagi', 'capek hidup',
            'hidup tidak ada artinya', 'hidup ga ada artinya', 'hidup gak ada artinya', 'aku beban',
            'saya beban', 'semua lebih baik tanpa aku', 'semua lebih baik tanpa saya', 'putus asa',
            'tidak ada harapan', 'tidak ada gunanya', 'tidak ada yang peduli', 'depresi berat',
            'hidup tidak berguna', 'saya tidak berguna', 'lebih baik saya tidak ada', 'lebih baik aku tidak ada',
            'tidak sanggup', 'tidak mampu', 'nyerah sama hidup', 'menyerah sama hidup', 'menyerah dengan hidup',
            'ingin hilang', 'pengen hilang', 'ingin ngilang', 'pengen ngilang', 'ingin kabur dari semua',
            'cuma beban', 'hanya beban', 'jadi beban', 'tidak punya alasan buat lanjut',
            'tidak punya alasan untuk lanjut', 'hidup rasanya kosong banget', 'tidak dibutuhkan siapa siapa',
            'capek sama semuanya', 'lelah sama semuanya',
        ];
        $mediumPhrases = [
            'cemas', 'khawatir', 'panik', 'overthinking', 'menangis terus', 'sering menangis',
            'tidak bisa tidur', 'susah tidur', 'sendirian', 'kesepian', 'tertekan', 'stress berat', 'stres berat',
            'serangan panik', 'sedang sedih', 'sangat sedih', 'lelah mental', 'gangguan mental',
            'kepala mau meledak', 'overload pikiran', 'murung', 'suasana hati buruk', 'menangis',
            'mental breakdown', 'bad day streak', 'chaos internal', 'overwhelmed parah', 'mode survival',
            'otak lowbat', 'otak lagi lowbat',
        ];

        if ($this->containsAny($normalized, $criticalPhrases)) {
            return ['label' => 4, 'confidence' => 0.98, 'match' => 'critical_phrase'];
        }

        if ($this->matchesAnyPattern($normalized, $criticalMethodPatterns)) {
            return ['label' => 4, 'confidence' => 0.97, 'match' => 'critical_method'];
        }

        if ($this->containsAny($normalized, $highPhrases)) {
            return ['label' => 3, 'confidence' => 0.92, 'match' => 'high_phrase'];
        }

        if ($hasSafeContext) {
            return ['label' => 0, 'confidence' => 0.15, 'match' => 'safe_context'];
        }

        if ($this->containsAny($normalized, $mediumPhrases)) {
            return ['label' => 2, 'confidence' => 0.86, 'match' => 'medium_phrase'];
        }

        return ['label' => 0, 'confidence' => 0.0, 'match' => null];
    }

    protected function normalizeText(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? $text;

        $phraseReplacements = [
            'logout dari dunia' => 'bunuh diri',
            'log out dari dunia' => 'bunuh diri',
            'logout dari bumi' => 'bunuh diri',
            'game over dari dunia' => 'bunuh diri',
            'end it all' => 'bunuh diri',
            'pengen end' => 'ingin mengakhiri hidup',
            'end aja' => 'mengakhiri hidup',
            'pengen ngilang selamanya' => 'bunuh diri',
            'pengen hilang selamanya' => 'bunuh diri',
            'pengen ngilang dari bumi' => 'bunuh diri',
            'pengen hilang dari bumi' => 'bunuh diri',
            'mau ngilang aja' => 'ingin hilang',
            'mau hilang aja' => 'ingin hilang',
            'mending gw ga ada' => 'lebih baik saya tidak ada',
            'mending gua ga ada' => 'lebih baik saya tidak ada',
            'mending gue ga ada' => 'lebih baik saya tidak ada',
            'mending aku ga ada' => 'lebih baik aku tidak ada',
            'hidup gw useless' => 'hidup saya tidak berguna',
            'hidup gua useless' => 'hidup saya tidak berguna',
            'hidup gue useless' => 'hidup saya tidak berguna',
            'aku useless' => 'saya tidak berguna',
            'nyerah sama hidup' => 'putus asa',
            'cape hidup' => 'putus asa',
            'capek idup' => 'putus asa',
            'capek hidup' => 'putus asa',
            'panic attack' => 'serangan panik',
            'mental breakdown' => 'gangguan mental',
            'down bgt' => 'sangat sedih',
            'down banget' => 'sangat sedih',
            'lagi down' => 'sedang sedih',
            'nangis mulu' => 'menangis terus',
            'ga bisa tidur' => 'tidak bisa tidur',
            'gak bisa tidur' => 'tidak bisa tidur',
            'g bisa tidur' => 'tidak bisa tidur',
        ];

        foreach ($phraseReplacements as $from => $to) {
            $text = str_replace($from, $to, $text);
        }

        $replacements = [
            'gw' => 'saya',
            'gue' => 'saya',
            'gua' => 'saya',
            'pgn' => 'pengen',
            'pngen' => 'pengen',
            'pgen' => 'pengen',
            'pngn' => 'pengen',
            'pengen' => 'pengen',
            'bundir' => 'bunuh diri',
            'bund1r' => 'bunuh diri',
            'bndr' => 'bunuh diri',
            'bnh dr' => 'bunuh diri',
            'unalive' => 'bunuh diri',
            'kys' => 'bunuh diri',
            'selfharm' => 'self harm',
            's harm' => 'self harm',
            'silet' => 'menyakiti diri',
            'sayat' => 'menyakiti diri',
            'ga' => 'gak',
            'g' => 'gak',
            'gk' => 'gak',
            'gak' => 'tidak',
            'gakuat' => 'tidak kuat',
            'gakwad' => 'tidak kuat',
            'gasanggup' => 'tidak sanggup',
            'gamampu' => 'tidak mampu',
            'ngga' => 'nggak',
            'ngak' => 'nggak',
            'tdk' => 'tidak',
            'tak' => 'tidak',
            'bunuhdiri' => 'bunuh diri',
            'cape' => 'capek',
            'anxiety' => 'cemas',
            'anxious' => 'cemas',
            'ovt' => 'overthinking',
            'overthink' => 'overthinking',
            'burnout' => 'lelah mental',
            'insomnia' => 'tidak bisa tidur',
            'insonniaaa' => 'tidak bisa tidur',
            'sad' => 'sedih',
            'badmood' => 'murung',
            'moody' => 'murung',
            'down' => 'sedih',
            'useless' => 'tidak berguna',
            'hopeless' => 'putus asa',
        ];

        foreach ($replacements as $from => $to) {
            $text = preg_replace('/\b' . preg_quote($from, '/') . '\b/u', $to, $text) ?? $text;
        }

        return preg_replace('/\s+/u', ' ', trim($text)) ?? $text;
    }

    protected function containsAny(string $text, array $phrases): bool
    {
        foreach ($phrases as $phrase) {
            if (str_contains($text, $phrase)) {
                return true;
            }
        }

        return false;
    }

    protected function matchesAnyPattern(string $text, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text) === 1) {
                return true;
            }
        }

        return false;
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
        $normalized = $this->normalizeText($text);
        $suggestions = [];

        if ($label >= 4) {
            return $this->fallbackSaran($label);
        }

        if ($label >= 3) {
            return array_slice(array_values(array_unique(array_merge($this->fallbackSaran($label), $apiSuggestions))), 0, 5);
        }

        if (str_contains($normalized, 'tidur') || str_contains($normalized, 'lelah') || str_contains($normalized, 'capek')) {
            $suggestions[] = 'Matikan layar 30 menit sebelum tidur dan usahakan tidur lebih awal malam ini';
            $suggestions[] = 'Lakukan peregangan ringan 10 menit untuk melepas tegang';
        }

        if (str_contains($normalized, 'cemas') || str_contains($normalized, 'khawatir') || str_contains($normalized, 'panik') || str_contains($normalized, 'overthinking')) {
            $suggestions[] = 'Coba teknik napas 4-7-8 selama 3 putaran';
            $suggestions[] = 'Lakukan grounding 5-4-3-2-1 untuk menenangkan pikiran';
        }

        if (str_contains($normalized, 'sendiri') || str_contains($normalized, 'kesepian') || str_contains($normalized, 'menangis')) {
            $suggestions[] = 'Kirim pesan ke satu teman atau keluarga yang kamu percaya';
            $suggestions[] = 'Tulis perasaanmu selama 10 menit tanpa menilai diri sendiri';
        }

        $suggestions = array_values(array_unique(array_merge($suggestions, $apiSuggestions, $this->fallbackSaran($label))));

        return array_slice($suggestions, 0, 5);
    }

    protected function adminStepsForRisk(int $label, array $apiSteps = []): array
    {
        $fallback = match (true) {
            $label >= 4 => [
                'Buka identitas darurat mahasiswa untuk memastikan kontak yang bisa dihubungi.',
                'Hubungi mahasiswa melalui nomor utama dengan bahasa tenang dan suportif.',
                'Jika mahasiswa tidak merespons, hubungi kontak darurat yang tercatat.',
                'Arahkan mahasiswa ke konselor kampus atau layanan darurat jika risiko masih aktif.',
                'Catat semua tindakan dan hasil komunikasi pada log tindak lanjut.',
            ],
            $label >= 3 => [
                'Tinjau cuplikan curhatan dan kata kunci risiko sebelum menghubungi mahasiswa.',
                'Hubungi mahasiswa secara suportif dan tawarkan bantuan konseling.',
                'Sarankan jadwal konseling kampus dalam 24-48 jam.',
                'Pantau perkembangan mahasiswa dan eskalasi jika kondisi memburuk.',
            ],
            default => [],
        };

        return array_slice(array_values(array_unique(array_merge($apiSteps, $fallback))), 0, 6);
    }

    public static function extractKeywordsStatic(string $text): array
    {
        return self::extractKeywords($text);
    }

    protected static function extractKeywords(string $text): array
    {
        $service = new self();
        $normalized = $service->normalizeText($text);
        $keywords = [];
        $dangerWords = [
            'mau bunuh diri', 'bunuh diri', 'mau mati', 'ingin mati', 'pengen mati', 'tidak ingin hidup',
            'tidak mau hidup', 'menyakiti diri', 'nyakitin diri', 'self harm', 'tidak kuat lagi',
            'capek hidup', 'tidak berguna', 'putus asa', 'tidak ada harapan', 'tidak ada gunanya',
            'tidak ada yang peduli', 'sendiri', 'depresi', 'cemas', 'tertekan', 'stress berat',
            'tidak bisa tidur', 'menangis', 'lompat dari gedung', 'loncat dari jembatan', 'terjun dari lantai',
            'minum racun', 'overdosis', 'minum obat banyak', 'nyilet', 'nyayat', 'tidur selamanya',
            'tidak bangun lagi', 'bye dunia', 'selamat tinggal dunia', 'pamitan dari dunia',
        ];

        foreach ($dangerWords as $word) {
            if (str_contains($normalized, $word)) {
                $keywords[] = $word;
            }
        }

        return array_slice(array_values(array_unique($keywords)), 0, 5);
    }
}
