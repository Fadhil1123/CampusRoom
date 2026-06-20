<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordHashTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_unhashed_password()
    {
        // Admin creates user with a plain text password
        $user = User::factory()->create([
            'nim_nip' => '1234567890',
            'password' => 'adminpass123',
        ]);

        $response = $this->post('/login', [
            'nim_nip' => '1234567890',
            'password' => 'adminpass123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertEquals($user->user_id, session('user')->user_id);
    }

    public function test_user_can_update_password_and_login_with_hashed_password()
    {
        $user = User::factory()->create([
            'nim_nip' => '1234567890',
            'password' => 'adminpass123',
        ]);

        // Login first to establish session for profile update
        $this->withSession(['user' => $user]);

        // Update password
        $response = $this->post('/profile/update-password', [
            'password_lama' => 'adminpass123',
            'password_baru' => 'newhashedpass123',
            'password_baru_confirmation' => 'newhashedpass123',
        ]);

        $response->assertSessionHas('success_password', 'Password berhasil diperbarui!');

        // Check if database now stores a hashed password
        $user->refresh();
        $this->assertNotEquals('newhashedpass123', $user->password);
        $this->assertTrue(\Hash::check('newhashedpass123', $user->password));

        // Try logging in with the new hashed password
        $loginResponse = $this->post('/login', [
            'nim_nip' => '1234567890',
            'password' => 'newhashedpass123',
        ]);

        $loginResponse->assertRedirect('/dashboard');
    }

    public function test_login_fails_with_incorrect_password()
    {
        User::factory()->create([
            'nim_nip' => '1234567890',
            'password' => 'adminpass123',
        ]);

        $response = $this->post('/login', [
            'nim_nip' => '1234567890',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHas('error', 'Login gagal');
    }
}
