<?php

namespace App\Filament\Resources\Leads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('source.name')
                    ->label('Source')
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label('Assignee')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('budget_min')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('budget_max')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('preferred_location')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->relationship(
                        name: 'status',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->active()->orderBy('name'),
                    )
                    ->searchable()
                    ->preload(),
                SelectFilter::make('source')
                    ->relationship(
                        name: 'source',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->active()->orderBy('name'),
                    )
                    ->searchable()
                    ->preload(),
                SelectFilter::make('assignee')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
