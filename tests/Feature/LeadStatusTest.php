<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_status_can_be_created_via_factory(): void
    {
        $status = LeadStatus::factory()->create([
            'name' => 'New',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas(LeadStatus::class, [
            'id' => $status->id,
            'name' => 'New',
            'is_active' => true,
        ]);
    }

    public function test_active_scope_returns_only_active_statuses(): void
    {
        $active = LeadStatus::factory()->create(['name' => 'Active Status']);
        LeadStatus::factory()->inactive()->create(['name' => 'Inactive Status']);

        $results = LeadStatus::query()->active()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($active));
    }

    public function test_lead_status_has_many_leads(): void
    {
        $status = LeadStatus::factory()->create();
        $leads = Lead::factory()->count(2)->create([
            'lead_status_id' => $status->id,
        ]);

        $this->assertCount(2, $status->leads);
        $this->assertTrue($status->leads->contains($leads->first()));
    }

    public function test_default_id_returns_active_default_status(): void
    {
        LeadStatus::factory()->create(['name' => 'Other']);
        $default = LeadStatus::factory()->asDefault()->create(['name' => 'Intake']);
        LeadStatus::factory()->inactive()->create(['name' => 'Inactive']);

        $this->assertSame($default->id, LeadStatus::defaultId());
    }

    public function test_default_id_ignores_inactive_default_status(): void
    {
        LeadStatus::factory()->asDefault()->inactive()->create(['name' => 'Inactive Default']);

        $this->assertNull(LeadStatus::defaultId());
    }

    public function test_marking_a_status_as_default_clears_the_previous_default(): void
    {
        $originalDefault = LeadStatus::factory()->asDefault()->create(['name' => 'Original']);
        $newDefault = LeadStatus::factory()->create(['name' => 'Replacement']);

        $newDefault->update(['is_default' => true]);

        $this->assertTrue($newDefault->fresh()->is_default);
        $this->assertFalse($originalDefault->fresh()->is_default);
    }
}
