<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_leads(): void
    {
        $user = User::factory()->create();
        $leads = Lead::factory()->count(3)->create();

        $this->actingAs($user);

        Livewire::test(ListLeads::class)
            ->assertOk()
            ->assertCanSeeTableRecords($leads);
    }

    public function test_can_create_a_lead(): void
    {
        $user = User::factory()->create();
        $status = LeadStatus::factory()->create(['name' => 'New']);
        $source = LeadSource::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateLead::class)
            ->assertOk()
            ->fillForm([
                'name' => 'Jordan Lee',
                'email' => 'jordan@example.com',
                'phone' => '555-0100',
                'lead_status_id' => $status->id,
                'lead_source_id' => $source->id,
                'assigned_to' => $user->id,
                'preferred_location' => 'Austin',
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas(Lead::class, [
            'name' => 'Jordan Lee',
            'email' => 'jordan@example.com',
            'phone' => '555-0100',
            'lead_status_id' => $status->id,
            'lead_source_id' => $source->id,
            'assigned_to' => $user->id,
            'preferred_location' => 'Austin',
        ]);
    }
}
