<?php

namespace Tests\Feature;

use App\Livewire\Admin\AlertPanel;
use App\Models\ActivityLog;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAlertFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_identity_and_handle_alert_with_logs(): void
    {
        $student = User::factory()->create(['is_admin' => false]);
        $admin = User::factory()->create(['is_admin' => true, 'nama' => 'Admin Test']);
        $alert = Alert::create([
            'user_id' => $student->id,
            'sumber' => 'buku_harian',
            'sumber_id' => 1,
            'label' => 4,
            'risk_level' => 'CRITICAL',
            'confidence' => 0.98,
            'kata_kunci' => ['bunuh diri'],
            'cuplikan_teks' => 'aku mau bunuh diri',
            'admin_steps' => ['Hubungi mahasiswa segera'],
            'is_handled' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(AlertPanel::class)
            ->call('openIdentity', $alert->id)
            ->assertSet('openedIdentities.' . $alert->id, true)
            ->call('handleAlert', $alert->id);

        $alert->refresh();

        $this->assertTrue($alert->identity_opened);
        $this->assertSame($admin->id, $alert->opened_by);
        $this->assertTrue($alert->is_handled);
        $this->assertSame($admin->id, $alert->handled_by);
        $this->assertSame(1, ActivityLog::where('aksi', 'identitas_dibuka')->count());
        $this->assertSame(1, ActivityLog::where('aksi', 'alert_ditindaklanjuti')->count());
    }
}
