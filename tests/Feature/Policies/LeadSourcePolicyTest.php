<?php

namespace Tests\Feature\Policies;

use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadSourcePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_any_authenticated_user_can_view_lead_sources(): void
    {
        $user = User::factory()->create();
        $source = LeadSource::factory()->create();

        $this->assertTrue($user->can('viewAny', LeadSource::class));
        $this->assertTrue($user->can('view', $source));
    }

    public function test_only_admins_can_create_update_or_delete_lead_sources(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $source = LeadSource::factory()->create();

        $this->assertTrue($admin->can('create', LeadSource::class));
        $this->assertTrue($admin->can('update', $source));
        $this->assertTrue($admin->can('delete', $source));
        $this->assertTrue($admin->can('deleteAny', LeadSource::class));

        $this->assertFalse($user->can('create', LeadSource::class));
        $this->assertFalse($user->can('update', $source));
        $this->assertFalse($user->can('delete', $source));
        $this->assertFalse($user->can('deleteAny', LeadSource::class));
    }
}
