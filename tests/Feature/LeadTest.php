<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_can_be_created_via_factory(): void
    {
        $status = LeadStatus::factory()->create(['name' => 'New']);

        $lead = Lead::factory()->create([
            'name' => 'Jane Doe',
            'lead_status_id' => $status->id,
        ]);

        $this->assertDatabaseHas(Lead::class, [
            'id' => $lead->id,
            'name' => 'Jane Doe',
            'lead_status_id' => $status->id,
        ]);

        $this->assertInstanceOf(LeadStatus::class, $lead->status);
        $this->assertInstanceOf(LeadSource::class, $lead->source);
    }

    public function test_lead_belongs_to_status_source_and_assignee(): void
    {
        $status = LeadStatus::factory()->create(['name' => 'Contacted']);
        $source = LeadSource::factory()->create(['name' => 'Referral']);
        $assignee = User::factory()->create(['name' => 'Agent Smith']);

        $lead = Lead::factory()
            ->assignedTo($assignee)
            ->create([
                'lead_status_id' => $status->id,
                'lead_source_id' => $source->id,
            ]);

        $this->assertTrue($lead->status->is($status));
        $this->assertTrue($lead->source->is($source));
        $this->assertTrue($lead->assignee->is($assignee));
        $this->assertTrue($assignee->assignedLeads->contains($lead));
        $this->assertTrue($status->leads->contains($lead));
        $this->assertTrue($source->leads->contains($lead));
    }
}
