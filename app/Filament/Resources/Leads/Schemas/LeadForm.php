<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\LeadSource;
use App\Models\LeadStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Select::make('lead_status_id')
                    ->label('Status')
                    ->relationship(
                        name: 'status',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->active()->orderBy('name'),
                    )
                    ->getOptionLabelUsing(function (Select $component): ?string {
                        $state = $component->getState();

                        if (blank($state)) {
                            return null;
                        }

                        return LeadStatus::query()->whereKey($state)->value('name');
                    })
                    ->default(fn (): ?int => LeadStatus::defaultId())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(LeadStatus::class),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                    ])
                    ->createOptionAction(fn (Action $action) => $action->authorize(
                        fn (): bool => Gate::allows('create', LeadStatus::class),
                    ))
                    ->createOptionUsing(function (array $data): int {
                        Gate::authorize('create', LeadStatus::class);

                        return LeadStatus::query()->create($data)->getKey();
                    }),
                Select::make('lead_source_id')
                    ->label('Source')
                    ->relationship(
                        name: 'source',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->active()->orderBy('name'),
                    )
                    ->getOptionLabelUsing(function (Select $component): ?string {
                        $state = $component->getState();

                        if (blank($state)) {
                            return null;
                        }

                        return LeadSource::query()->whereKey($state)->value('name');
                    })
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(LeadSource::class),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                    ])
                    ->createOptionAction(fn (Action $action) => $action->authorize(
                        fn (): bool => Gate::allows('create', LeadSource::class),
                    ))
                    ->createOptionUsing(function (array $data): int {
                        Gate::authorize('create', LeadSource::class);

                        return LeadSource::query()->create($data)->getKey();
                    }),
                Select::make('assigned_to')
                    ->label('Assignee')
                    ->relationship(
                        name: 'assignee',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'),
                    )
                    ->searchable()
                    ->preload(),
                TextInput::make('budget_min')
                    ->label('Budget min')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('budget_max')
                    ->label('Budget max')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('preferred_location')
                    ->maxLength(255),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
