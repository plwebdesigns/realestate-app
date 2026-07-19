<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_are_not_admins_by_default(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->is_admin);
        $this->assertFalse($user->isAdmin());
        $this->assertDatabaseHas(User::class, [
            'id' => $user->id,
            'is_admin' => false,
        ]);
    }

    public function test_admin_factory_state_marks_user_as_admin(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertTrue($user->is_admin);
        $this->assertTrue($user->isAdmin());
    }
}
