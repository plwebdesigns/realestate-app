<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadSourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_source_can_be_created_via_factory(): void
    {
        $source = LeadSource::factory()->create([
            'name' => 'Website',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas(LeadSource::class, [
            'id' => $source->id,
            'name' => 'Website',
            'is_active' => true,
        ]);
    }

    public function test_active_scope_returns_only_active_sources(): void
    {
        $active = LeadSource::factory()->create(['name' => 'Active Source']);
        LeadSource::factory()->inactive()->create(['name' => 'Inactive Source']);

        $results = LeadSource::query()->active()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($active));
    }

    public function test_lead_source_has_many_leads(): void
    {
        $source = LeadSource::factory()->create();
        $leads = Lead::factory()->count(2)->create([
            'lead_source_id' => $source->id,
        ]);

        $this->assertCount(2, $source->leads);
        $this->assertTrue($source->leads->contains($leads->first()));
    }
}
