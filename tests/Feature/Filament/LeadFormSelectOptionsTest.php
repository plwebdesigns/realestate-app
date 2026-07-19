<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadFormSelectOptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_lead_form_source_select_excludes_inactive_sources(): void
    {
        $user = User::factory()->create();
        $activeSource = LeadSource::factory()->create(['name' => 'Active Source']);
        $inactiveSource = LeadSource::factory()->inactive()->create(['name' => 'Inactive Source']);

        $this->actingAs($user);

        Livewire::test(CreateLead::class)
            ->assertOk()
            ->assertFormFieldExists('lead_source_id', function (Select $field) use ($activeSource, $inactiveSource): bool {
                $options = $field->getOptions();

                return array_key_exists($activeSource->id, $options)
                    && ! array_key_exists($inactiveSource->id, $options);
            });
    }

    public function test_edit_lead_form_source_select_excludes_inactive_sources(): void
    {
        $user = User::factory()->create();
        $activeSource = LeadSource::factory()->create(['name' => 'Active Source']);
        $inactiveSource = LeadSource::factory()->inactive()->create(['name' => 'Inactive Source']);
        $lead = Lead::factory()->create([
            'lead_source_id' => $activeSource->id,
        ]);

        $this->actingAs($user);

        Livewire::test(EditLead::class, ['record' => $lead->getRouteKey()])
            ->assertOk()
            ->assertFormFieldExists('lead_source_id', function (Select $field) use ($activeSource, $inactiveSource): bool {
                $options = $field->getOptions();

                return array_key_exists($activeSource->id, $options)
                    && ! array_key_exists($inactiveSource->id, $options);
            });
    }

    public function test_edit_lead_form_status_select_excludes_inactive_statuses(): void
    {
        $user = User::factory()->create();
        $activeStatus = LeadStatus::factory()->create(['name' => 'Active Status']);
        $inactiveStatus = LeadStatus::factory()->inactive()->create(['name' => 'Inactive Status']);
        $lead = Lead::factory()->create([
            'lead_status_id' => $activeStatus->id,
        ]);

        $this->actingAs($user);

        Livewire::test(EditLead::class, ['record' => $lead->getRouteKey()])
            ->assertOk()
            ->assertFormFieldExists('lead_status_id', function (Select $field) use ($activeStatus, $inactiveStatus): bool {
                $options = $field->getOptions();

                return array_key_exists($activeStatus->id, $options)
                    && ! array_key_exists($inactiveStatus->id, $options);
            });
    }

    public function test_edit_lead_form_shows_label_for_currently_selected_inactive_source_without_listing_it(): void
    {
        $user = User::factory()->create();
        $inactiveSource = LeadSource::factory()->inactive()->create(['name' => 'Legacy Source']);
        $activeSource = LeadSource::factory()->create(['name' => 'Active Source']);
        $lead = Lead::factory()->create([
            'lead_source_id' => $inactiveSource->id,
        ]);

        $this->actingAs($user);

        Livewire::test(EditLead::class, ['record' => $lead->getRouteKey()])
            ->assertOk()
            ->assertFormSet([
                'lead_source_id' => $inactiveSource->id,
            ])
            ->assertFormFieldExists('lead_source_id', function (Select $field) use ($activeSource, $inactiveSource): bool {
                $options = $field->getOptions();

                return array_key_exists($activeSource->id, $options)
                    && ! array_key_exists($inactiveSource->id, $options)
                    && $field->getOptionLabel() === $inactiveSource->name;
            });
    }

    public function test_leads_table_source_filter_options_exclude_inactive_sources(): void
    {
        $user = User::factory()->create();
        LeadSource::factory()->create(['name' => 'Active Filter Source']);
        LeadSource::factory()->inactive()->create(['name' => 'Inactive Filter Source']);

        $this->actingAs($user);

        Livewire::test(ListLeads::class)
            ->assertOk()
            ->assertSee('Active Filter Source')
            ->assertDontSee('Inactive Filter Source');
    }

    public function test_leads_table_status_filter_options_exclude_inactive_statuses(): void
    {
        $user = User::factory()->create();
        LeadStatus::factory()->create(['name' => 'Active Filter Status']);
        LeadStatus::factory()->inactive()->create(['name' => 'Inactive Filter Status']);

        $this->actingAs($user);

        Livewire::test(ListLeads::class)
            ->assertOk()
            ->assertSee('Active Filter Status')
            ->assertDontSee('Inactive Filter Status');
    }
}
