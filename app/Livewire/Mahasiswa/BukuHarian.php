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
        $label = $result['label'] ?? 0;
        $safeOutput = (new AIService())->buildSafeOutput($this->newEntry, $label);

        // Update entri dengan hasil analisis
        $entry->update([
            'ai_reply' => $result['ai_reply'] ?? $safeOutput['ai_reply'],
            'ai_saran' => $result['ai_saran'] ?? $safeOutput['ai_saran'],
            'label' => $label,
            'risk_level' => $result['risk_level'] ?? 'LOW',
            'confidence' => $result['confidence'] ?? 0,
            'analysis_metadata' => $result['analysis_metadata'] ?? null,
            'is_analyzed' => true,
        ]);

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
