<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use App\Models\BukuHarian as BukuHarianModel;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;

class BukuHarian extends Component
{
    public string $newEntry = '';
    public bool $isAnalyzing = false;

    public function simpan(): void
    {
        $this->validate([
            'newEntry' => 'required|string|min:3|max:5000',
        ]);

        // Simpan entri ke DB
        $entry = BukuHarianModel::create([
            'user_id' => Auth::id(),
            'isi' => $this->newEntry,
            'is_analyzed' => false,
        ]);

        $this->isAnalyzing = true;

        // Gunakan AIService untuk analisis dan auto-alert
        $result = AIService::processAndAlert(
            $this->newEntry,
            Auth::user(),
            'buku_harian',
            $entry->id
        );

        // Update entri dengan hasil analisis
        $entry->update([
            'ai_reply' => $result['ai_reply'] ?? $this->fallbackReply(),
            'ai_saran' => $result['ai_saran'] ?? $this->fallbackSaran(),
            'label' => $result['label'] ?? 0,
            'risk_level' => $result['risk_level'] ?? 'LOW',
            'confidence' => $result['confidence'] ?? 0,
            'is_analyzed' => true,
        ]);

        $this->isAnalyzing = false;
        $this->newEntry = '';
    }

    /**
     * Fallback reply saat AI tidak tersedia.
     */
    private function fallbackReply(): string
    {
        return 'Terima kasih sudah berbagi perasaanmu hari ini. Kamu tidak sendirian — selalu ada yang peduli padamu. 💙';
    }

    /**
     * Fallback saran saat AI tidak tersedia.
     */
    private function fallbackSaran(): array
    {
        return [
            'Jalan-jalan santai di luar rumah selama 15 menit',
            'Tulis 3 hal yang kamu syukuri hari ini',
            'Dengarkan musik favoritmu',
        ];
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
