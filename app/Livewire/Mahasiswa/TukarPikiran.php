<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\ForumLike;
use App\Models\Alert;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TukarPikiran extends Component
{
    public string $newPost = '';
    public bool $showForm = false;
    public ?int $replyingTo = null;
    public string $replyText = '';

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        $this->newPost = '';
    }

    public function posting(): void
    {
        $this->validate([
            'newPost' => 'required|string|min:3|max:5000',
        ]);

        $label = 0;
        $riskLevel = 'LOW';
        $isHidden = false;

        // Coba panggil FastAPI untuk analisis
        try {
            $baseUrl = config('services.fastapi.url', 'http://127.0.0.1:8000');
            $response = Http::timeout(30)->post("{$baseUrl}/predict", [
                'text' => $this->newPost,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $label = $result['label'] ?? 0;
                $riskLevel = $result['risk_level'] ?? 'LOW';

                if ($label >= 3) {
                    $isHidden = true;
                }
            }
        } catch (\Exception $e) {
            // Lanjut tanpa AI jika tidak tersedia
        }

        $post = ForumPost::create([
            'user_id'    => Auth::id(),
            'konten'     => $this->newPost,
            'label'      => $label,
            'risk_level' => $riskLevel,
            'is_hidden'  => $isHidden,
        ]);

        // Jika label >= 3 → buat Alert
        if ($label >= 3) {
            $alert = Alert::create([
                'user_id'       => Auth::id(),
                'sumber'        => 'forum_post',
                'sumber_id'     => $post->id,
                'label'         => $label,
                'risk_level'    => $riskLevel,
                'confidence'    => 0.85,
                'kata_kunci'    => ['forum berisiko'],
                'cuplikan_teks' => mb_substr($this->newPost, 0, 200),
                'is_handled'    => false,
            ]);

            ActivityLog::create([
                'aksi'           => 'alert_dibuat',
                'severity'       => $label == 4 ? 'kritis' : 'waspada',
                'alert_id'       => $alert->id,
                'target_user_id' => Auth::id(),
                'actor_label'    => 'Sistem',
                'detail'         => 'Alert otomatis: postingan forum terdeteksi berisiko',
            ]);
        }

        $this->newPost = '';
        $this->showForm = false;
    }

    public function toggleLike(int $postId): void
    {
        $existing = ForumLike::where('post_id', $postId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            ForumLike::create([
                'post_id' => $postId,
                'user_id' => Auth::id(),
            ]);
        }
    }

    public function startReply(int $postId): void
    {
        $this->replyingTo = $this->replyingTo === $postId ? null : $postId;
        $this->replyText = '';
    }

    public function kirimBalasan(int $postId): void
    {
        $this->validate([
            'replyText' => 'required|string|min:1|max:2000',
        ]);

        ForumReply::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'konten'  => $this->replyText,
        ]);

        $this->replyText = '';
        $this->replyingTo = null;
    }

    public function render()
    {
        $posts = ForumPost::where('is_hidden', false)
            ->with(['user:id,username_anonim', 'replies.user:id,username_anonim', 'likes'])
            ->withCount(['replies', 'likes'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.mahasiswa.tukar-pikiran', [
            'posts' => $posts,
        ]);
    }
}
