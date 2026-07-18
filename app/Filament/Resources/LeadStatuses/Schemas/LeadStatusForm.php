<?php

namespace App\Filament\Resources\LeadStatuses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LeadStatusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
                Toggle::make('is_default')
                    ->label('Default for new leads')
                    ->helperText('Only one status can be the default. Enabling this clears the previous default.')
                    ->default(false)
                    ->required(),
            ]);
    }
}
