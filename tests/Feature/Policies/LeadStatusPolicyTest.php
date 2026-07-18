<?php

namespace Tests\Feature\Policies;

use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadStatusPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_any_authenticated_user_can_view_lead_statuses(): void
    {
        $user = User::factory()->create();
        $status = LeadStatus::factory()->create();

        $this->assertTrue($user->can('viewAny', LeadStatus::class));
        $this->assertTrue($user->can('view', $status));
    }

    public function test_only_admins_can_create_update_or_delete_lead_statuses(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $status = LeadStatus::factory()->create();

        $this->assertTrue($admin->can('create', LeadStatus::class));
        $this->assertTrue($admin->can('update', $status));
        $this->assertTrue($admin->can('delete', $status));
        $this->assertTrue($admin->can('deleteAny', LeadStatus::class));

        $this->assertFalse($user->can('create', LeadStatus::class));
        $this->assertFalse($user->can('update', $status));
        $this->assertFalse($user->can('delete', $status));
        $this->assertFalse($user->can('deleteAny', LeadStatus::class));
    }
}
