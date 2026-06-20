<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test updating profile textual details successfully.
     */
    public function test_user_can_update_profile_text_fields(): void
    {
        $user = User::factory()->create([
            'nama' => 'Original Name',
            'email' => 'original@example.com',
            'role' => 'user',
        ]);

        $response = $this->withSession(['user' => $user])
            ->post('/profile/update', [
                'nama' => 'New Name',
                'email' => 'new@example.com',
                'no_hp' => '081234567890',
                'jurusan' => 'Teknik Informatika',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Profil berhasil diperbarui!');

        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'nama' => 'New Name',
            'email' => 'new@example.com',
            'no_hp' => '081234567890',
            'jurusan' => 'Teknik Informatika',
        ]);

        $this->assertEquals('New Name', session('user')->nama);
    }

    /**
     * Test updating only the profile photo (without supplying text fields).
     */
    public function test_user_can_update_only_profile_photo(): void
    {
        $user = User::factory()->create([
            'nama' => 'Keep Name',
            'role' => 'user',
            'foto' => null,
        ]);

        $file = UploadedFile::fake()->createWithContent('avatar.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));

        $response = $this->withSession(['user' => $user])
            ->post('/profile/update', [
                'foto' => $file,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Profil berhasil diperbarui!');

        // Check if database updated and user fresh was loaded
        $user->refresh();
        $this->assertNotNull($user->foto);
        $this->assertEquals($user->foto, session('user')->foto);

        Storage::disk('public')->assertExists($user->foto);
    }

    /**
     * Test old photo deletion when a new one is uploaded.
     */
    public function test_old_photo_is_deleted_when_new_photo_is_uploaded(): void
    {
        $oldPhotoPath = 'profile-photos/old-avatar.jpg';
        Storage::disk('public')->put($oldPhotoPath, 'old content');

        $user = User::factory()->create([
            'nama' => 'Photo User',
            'role' => 'user',
            'foto' => $oldPhotoPath,
        ]);

        Storage::disk('public')->assertExists($oldPhotoPath);

        $file = UploadedFile::fake()->createWithContent('new-avatar.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));

        $response = $this->withSession(['user' => $user])
            ->post('/profile/update', [
                'foto' => $file,
            ]);

        $response->assertStatus(302);

        $user->refresh();
        $this->assertNotEquals($oldPhotoPath, $user->foto);
        Storage::disk('public')->assertMissing($oldPhotoPath);
        Storage::disk('public')->assertExists($user->foto);
    }

    /**
     * Test validation fails when submitting text profile with empty name.
     */
    public function test_validation_fails_when_updating_profile_without_required_name(): void
    {
        $user = User::factory()->create([
            'nama' => 'Original Name',
            'role' => 'user',
        ]);

        $response = $this->withSession(['user' => $user])
            ->post('/profile/update', [
                'nama' => '',
                'email' => 'new@example.com',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['nama']);
    }
}
