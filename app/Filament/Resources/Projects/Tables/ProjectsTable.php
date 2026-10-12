<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatusEnum;
use App\Filament\Support\Actions\CommonActions;
use App\Filament\Support\Columns\CommonColumns;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['client', 'status', 'approvedQuote']))
            ->searchPlaceholder('Buscar obra por título o código...')
            ->columns([
                CommonColumns::recordTitleWithCode('title', 'code', 'PROYECTO'),
                CommonColumns::client('client', 'CLIENTE'),
                CommonColumns::activeBaseline('approvedQuote.title', 'LÍNEA BASE ACTIVA'),
                CommonColumns::statusBadge('status.code', 'ESTADO'),
            ])
            ->filters([
                SelectFilter::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->company_name ?: "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('createQuote')
                    ->label('Formular cotización')
                    ->icon('heroicon-m-document-plus')
                    ->color('primary')
                    ->url(fn (Project $record): string => url("/admin/quotes/create?project_id={$record->id}"))
                    ->visible(fn (Project $record): bool => ! in_array($record->status?->code, [
                        ProjectStatusEnum::COMPLETED,
                        ProjectStatusEnum::CANCELED,
                    ], true)),

                CommonActions::editRowAction(),
            ])
            ->toolbarActions([]);
    }
}
