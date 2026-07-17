<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\LeadSources\Pages\CreateLeadSource;
use App\Filament\Resources\LeadSources\Pages\ListLeadSources;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadSourceResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_lead_sources(): void
    {
        $user = User::factory()->create();
        $sources = LeadSource::factory()->count(3)->create();

        $this->actingAs($user);

        Livewire::test(ListLeadSources::class)
            ->assertOk()
            ->assertCanSeeTableRecords($sources);
    }

    public function test_can_create_a_lead_source(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateLeadSource::class)
            ->assertOk()
            ->fillForm([
                'name' => 'Open House',
                'is_active' => true,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas(LeadSource::class, [
            'name' => 'Open House',
            'is_active' => true,
        ]);
    }
}
