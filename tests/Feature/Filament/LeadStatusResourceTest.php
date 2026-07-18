<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\LeadStatuses\Pages\CreateLeadStatus;
use App\Filament\Resources\LeadStatuses\Pages\ListLeadStatuses;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadStatusResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_lead_statuses(): void
    {
        $user = User::factory()->create();
        $statuses = LeadStatus::factory()->count(3)->create();

        $this->actingAs($user);

        Livewire::test(ListLeadStatuses::class)
            ->assertOk()
            ->assertCanSeeTableRecords($statuses);
    }

    public function test_can_create_a_lead_status(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateLeadStatus::class)
            ->assertOk()
            ->fillForm([
                'name' => 'Qualified',
                'is_active' => true,
                'is_default' => false,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        $this->assertDatabaseHas(LeadStatus::class, [
            'name' => 'Qualified',
            'is_active' => true,
            'is_default' => false,
        ]);
    }
}
