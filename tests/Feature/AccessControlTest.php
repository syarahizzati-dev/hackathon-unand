<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_protected_pages(): void
    {
        foreach (['/student-dashboard', '/mood', '/buku-harian', '/tukar-pikiran', '/profile', '/admin-dashboard', '/admin-alert', '/admin-log'] as $path) {
            $this->get($path)->assertRedirect('/');
        }
    }

    public function test_mahasiswa_cannot_access_admin_pages(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        foreach (['/admin-dashboard', '/admin-alert', '/admin-log'] as $path) {
            $this->actingAs($user)->get($path)->assertForbidden();
        }
    }

    public function test_admin_is_redirected_from_mahasiswa_pages(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        foreach (['/student-dashboard', '/mood', '/buku-harian', '/tukar-pikiran', '/profile'] as $path) {
            $this->actingAs($admin)->get($path)->assertRedirect('/admin-dashboard');
        }
    }
}
