<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://localhost']);
    }

    public function test_profile_photo_accessor_returns_stream_route_when_file_exists(): void
    {
        Storage::fake('public');

        $user = $this->makeUser('profile/avatar.jpg');

        Storage::disk('public')->put('profile/avatar.jpg', 'avatar-content');

        $this->assertSame(
            route('profile.photo.show', ['path' => 'profile/avatar.jpg']),
            $user->photo_url
        );
    }

    public function test_profile_photo_route_streams_stored_file(): void
    {
        Storage::fake('public');

        $user = $this->makeUser('profile/avatar.jpg');

        Storage::disk('public')->put('profile/avatar.jpg', 'avatar-content');

        $this->actingAs($user);

        $response = $this->get(route('profile.photo.show', ['path' => $user->photo]));

        $response->assertOk();
        $this->assertSame('avatar-content', $response->streamedContent());
    }

    public function test_profile_photo_route_returns_not_found_when_missing(): void
    {
        Storage::fake('public');

        $user = $this->makeUser('profile/missing.jpg');

        $this->actingAs($user);

        $this->get(route('profile.photo.show', ['path' => $user->photo]))
            ->assertNotFound();
    }

    private function makeUser(string $photo): User
    {
        $user = new User();
        $user->id = 1;
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->password = bcrypt('password');
        $user->photo = $photo;

        return $user;
    }
}
