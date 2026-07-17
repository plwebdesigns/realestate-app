<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_can_be_created_via_factory(): void
    {
        $lead = Lead::factory()->create([
            'name' => 'Jane Doe',
            'status' => LeadStatus::New,
        ]);

        $this->assertDatabaseHas(Lead::class, [
            'id' => $lead->id,
            'name' => 'Jane Doe',
            'status' => LeadStatus::New->value,
        ]);

        $this->assertInstanceOf(LeadStatus::class, $lead->status);
        $this->assertInstanceOf(LeadSource::class, $lead->source);
    }

    public function test_lead_belongs_to_source_and_assignee(): void
    {
        $source = LeadSource::factory()->create(['name' => 'Referral']);
        $assignee = User::factory()->create(['name' => 'Agent Smith']);

        $lead = Lead::factory()
            ->assignedTo($assignee)
            ->create([
                'lead_source_id' => $source->id,
            ]);

        $this->assertTrue($lead->source->is($source));
        $this->assertTrue($lead->assignee->is($assignee));
        $this->assertTrue($assignee->assignedLeads->contains($lead));
        $this->assertTrue($source->leads->contains($lead));
    }

    public function test_lead_defaults_status_to_new(): void
    {
        $lead = new Lead(['name' => 'New Lead']);

        $this->assertSame(LeadStatus::New, $lead->status);
    }
}
