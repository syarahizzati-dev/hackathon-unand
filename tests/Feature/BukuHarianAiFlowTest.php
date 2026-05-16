<?php

namespace Tests\Feature;

use App\Livewire\Mahasiswa\BukuHarian;
use App\Models\Alert;
use App\Models\BukuHarian as BukuHarianModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class BukuHarianAiFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_risk_diary_is_saved_without_alert(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $user = $this->makeUser('low@example.com', '100001');

        Livewire::actingAs($user)
            ->test(BukuHarian::class)
            ->set('newEntry', 'hari ini baik dan aku senang bisa selesai tugas')
            ->call('simpan')
            ->assertSet('newEntry', '');

        $entry = BukuHarianModel::firstOrFail();

        $this->assertSame(0, $entry->label);
        $this->assertSame('LOW', $entry->risk_level);
        $this->assertNotEmpty($entry->ai_reply);
        $this->assertNotEmpty($entry->ai_saran);
        $this->assertSame(0, $entry->analysis_metadata['final_label']);
        $this->assertFalse(Alert::exists());
    }

    public function test_high_risk_diary_creates_alert_with_admin_steps(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $user = $this->makeUser('high@example.com', '100002');

        Livewire::actingAs($user)
            ->test(BukuHarian::class)
            ->set('newEntry', 'aku ga punya alasan buat lanjut')
            ->call('simpan');

        $entry = BukuHarianModel::firstOrFail();
        $alert = Alert::firstOrFail();

        $this->assertSame(3, $entry->label);
        $this->assertSame('HIGH', $entry->risk_level);
        $this->assertSame('high_phrase', $entry->analysis_metadata['local_match']);
        $this->assertMatchesRegularExpression('/dukungan|konselor|orang.*percaya|bantuan|sendirian/i', $entry->ai_reply);
        $this->assertSame(3, $alert->label);
        $this->assertSame('HIGH', $alert->risk_level);
        $this->assertNotEmpty($alert->admin_steps);
        $this->assertSame(3, $alert->analysis_metadata['final_label']);
    }

    public function test_critical_diary_creates_alert_with_crisis_safe_output(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $user = $this->makeUser('critical@example.com', '100003');

        Livewire::actingAs($user)
            ->test(BukuHarian::class)
            ->set('newEntry', 'aku mau minum racun')
            ->call('simpan');

        $entry = BukuHarianModel::firstOrFail();
        $alert = Alert::firstOrFail();

        $this->assertSame(4, $entry->label);
        $this->assertSame('CRITICAL', $entry->risk_level);
        $this->assertSame('critical_method', $entry->analysis_metadata['local_match']);
        $this->assertMatchesRegularExpression('/segera|jangan sendirian|kontak darurat|konselor|layanan darurat|orang terdekat/i', $entry->ai_reply);
        $this->assertNotSame('Jalan santai', $entry->ai_saran[0] ?? null);
        $this->assertSame(4, $alert->label);
        $this->assertSame('CRITICAL', $alert->risk_level);
        $this->assertNotEmpty($alert->admin_steps);
        $this->assertSame('fallback', $alert->analysis_metadata['reply_source']);
    }

    private function makeUser(string $email, string $nim): User
    {
        return User::create([
            'nama' => 'Mahasiswa Test',
            'nim' => $nim,
            'email' => $email,
            'no_telepon' => '081234567890',
            'jurusan' => 'Teknik',
            'program_studi' => 'Informatika',
            'kontak_darurat' => '081298765432',
            'password' => Hash::make('password'),
            'username_anonim' => 'AnonTest',
            'is_admin' => false,
        ]);
    }
}
