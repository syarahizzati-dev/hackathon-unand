<?php

namespace Tests\Feature;

use App\Livewire\Mahasiswa\MoodTracker;
use App\Livewire\Mahasiswa\SelfCheckForm;
use App\Models\Alert;
use App\Models\SelfCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class SelfCheckMoodFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_positive_self_check_stays_low_and_updates_mood_tracker(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $user = User::factory()->create(['is_admin' => false]);

        $component = Livewire::actingAs($user)->test(SelfCheckForm::class);

        foreach (range(1, 5) as $question) {
            $component->call('selectAnswer', $question, 5);
        }

        $component->assertSet('readyToSubmit', true)
            ->call('submit')
            ->assertSet('isComplete', true);

        $check = SelfCheck::firstOrFail();

        $this->assertSame(25, $check->skor_total);
        $this->assertSame(0, $check->label);
        $this->assertSame('LOW', $check->risk_level);
        $this->assertFalse(Alert::exists());

        Livewire::actingAs($user)
            ->test(MoodTracker::class)
            ->assertSee('25/25')
            ->assertSee('LOW');
    }

    public function test_critical_self_check_creates_alert_with_admin_steps(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $user = User::factory()->create(['is_admin' => false]);

        $component = Livewire::actingAs($user)->test(SelfCheckForm::class);

        foreach (range(1, 5) as $question) {
            $component->call('selectAnswer', $question, 1);
        }

        $component->call('submit');

        $check = SelfCheck::firstOrFail();
        $alert = Alert::firstOrFail();

        $this->assertSame(5, $check->skor_total);
        $this->assertSame(4, $check->label);
        $this->assertSame('CRITICAL', $check->risk_level);
        $this->assertSame('self_check', $alert->sumber);
        $this->assertNotEmpty($alert->admin_steps);
        $this->assertSame(4, $alert->analysis_metadata['score_label']);
        $this->assertSame(4, $alert->analysis_metadata['final_label']);
    }
}
