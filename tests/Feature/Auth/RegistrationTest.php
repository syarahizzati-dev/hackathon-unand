<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('nama', 'Test User')
            ->set('nim', '1234567890')
            ->set('email', 'test@example.com')
            ->set('no_telepon', '08123456789')
            ->set('jurusan', 'Teknologi Informasi')
            ->set('program_studi', 'D3 Manajemen Informatika')
            ->set('kontak_darurat', '08987654321')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('login', absolute: false));

        $this->assertGuest();
    }
}
