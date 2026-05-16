<?php

namespace Tests\Feature;

use App\Livewire\Mahasiswa\TukarPikiran;
use App\Models\Alert;
use App\Models\ForumLike;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ForumFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_post_like_reply_and_nested_reply(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $user = User::factory()->create(['is_admin' => false]);

        Livewire::actingAs($user)
            ->test(TukarPikiran::class)
            ->call('toggleForm')
            ->set('newPost', 'hari ini aku mau berbagi cerita baik')
            ->call('posting')
            ->assertSet('newPost', '')
            ->assertSet('showForm', false);

        $post = ForumPost::firstOrFail();
        $this->assertFalse($post->is_hidden);
        $this->assertFalse(Alert::exists());

        Livewire::actingAs($user)
            ->test(TukarPikiran::class)
            ->call('toggleLike', $post->id)
            ->call('startReply', $post->id)
            ->set('replyText', 'terima kasih sudah berbagi')
            ->call('kirimBalasan', $post->id);

        $reply = ForumReply::firstOrFail();

        Livewire::actingAs($user)
            ->test(TukarPikiran::class)
            ->call('startReplyToReply', $post->id, $reply->id)
            ->set('replyText', 'aku setuju dengan balasan ini')
            ->call('kirimBalasan', $post->id, $reply->id);

        $this->assertSame(1, ForumLike::count());
        $this->assertSame(2, ForumReply::count());
        $this->assertSame($reply->id, ForumReply::latest('id')->first()->parent_id);
    }

    public function test_high_risk_forum_post_is_hidden_and_creates_alert(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $user = User::factory()->create(['is_admin' => false]);

        Livewire::actingAs($user)
            ->test(TukarPikiran::class)
            ->call('toggleForm')
            ->set('newPost', 'aku mau minum racun')
            ->call('posting');

        $post = ForumPost::firstOrFail();
        $alert = Alert::firstOrFail();

        $this->assertTrue($post->is_hidden);
        $this->assertSame(4, $post->label);
        $this->assertSame('CRITICAL', $post->risk_level);
        $this->assertSame('forum_post', $alert->sumber);
        $this->assertNotEmpty($alert->admin_steps);
        $this->assertSame(4, $alert->analysis_metadata['final_label']);
    }
}
