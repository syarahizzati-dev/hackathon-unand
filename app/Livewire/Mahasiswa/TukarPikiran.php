<?php

namespace App\Livewire\Mahasiswa;

use Livewire\Component;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\ForumLike;
use App\Models\Alert;
use App\Models\ActivityLog;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;

class TukarPikiran extends Component
{
    public string $newPost = '';
    public bool $showForm = false;
    public ?int $replyingToPost = null;
    public ?int $replyingToReply = null;
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

        // Analisis via AIService (single prediction call)
        $service = new AIService();
        $result = $service->predict($this->newPost);

        $label = $result['label'] ?? 0;
        $riskLevel = $result['risk_level'] ?? 'LOW';
        $confidence = $result['confidence'] ?? 0;
        $isHidden = $label >= 3;

        $post = ForumPost::create([
            'user_id'    => Auth::id(),
            'konten'     => $this->newPost,
            'label'      => $label,
            'risk_level' => $riskLevel,
            'is_hidden'  => $isHidden,
        ]);

        // Jika label >= 3 → buat Alert langsung (tanpa double prediction)
        if ($label >= 3) {
            $alert = Alert::create([
                'user_id'       => Auth::id(),
                'sumber'        => 'forum_post',
                'sumber_id'     => $post->id,
                'label'         => $label,
                'risk_level'    => $riskLevel,
                'confidence'    => $confidence,
                'kata_kunci'    => AIService::extractKeywordsStatic($this->newPost),
                'cuplikan_teks' => mb_substr($this->newPost, 0, 200),
                'admin_steps' => $result['admin_steps'] ?? $service->buildSafeOutput($this->newPost, $label)['admin_steps'],
                'analysis_metadata' => $result['analysis_metadata'] ?? null,
                'is_handled'    => false,
            ]);

            ActivityLog::create([
                'aksi'           => 'alert_dibuat',
                'severity'       => $label >= 4 ? 'kritis' : 'waspada',
                'alert_id'       => $alert->id,
                'target_user_id' => Auth::id(),
                'actor_label'    => 'Sistem',
                'detail'         => 'Alert otomatis: postingan forum terdeteksi berisiko (' . $riskLevel . ')',
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
        $this->replyingToPost = $this->replyingToPost === $postId && $this->replyingToReply === null ? null : $postId;
        $this->replyingToReply = null;
        $this->replyText = '';
    }

    public function startReplyToReply(int $postId, int $replyId): void
    {
        $this->replyingToPost = $postId;
        $this->replyingToReply = $this->replyingToReply === $replyId ? null : $replyId;
        $this->replyText = '';
    }

    public function kirimBalasan(int $postId, ?int $parentId = null): void
    {
        $this->validate([
            'replyText' => 'required|string|min:1|max:2000',
        ]);

        if ($parentId !== null) {
            ForumReply::where('post_id', $postId)->findOrFail($parentId);
        }

        ForumReply::create([
            'post_id'   => $postId,
            'user_id'   => Auth::id(),
            'parent_id' => $parentId,
            'konten'    => $this->replyText,
        ]);

        $this->replyText = '';
        $this->replyingToPost = null;
        $this->replyingToReply = null;
    }

    public function render()
    {
        $posts = ForumPost::where('is_hidden', false)
            ->with([
                'user:id,username_anonim',
                'replies.user:id,username_anonim',
                'replies.children.user:id,username_anonim',
                'likes',
            ])
            ->withCount(['allReplies as replies_count', 'likes'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.mahasiswa.tukar-pikiran', [
            'posts' => $posts,
        ]);
    }
}
