<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.users.nombre'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('admin.users.correo_electronico'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role.name')
                    ->label(__('admin.users.rol'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'company' => 'warning',
                        'candidate' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('admin.users.registrado'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('verification_requests')
                    ->label(__('shared.verification.requests_filter'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('verification_requested_at')->whereNull('verified_at')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                // Only admins hand out the verified check.
                Action::make('verify')
                    ->label(__('shared.verification.verify'))
                    ->icon(Heroicon::CheckBadge)
                    ->color('primary')
                    ->requiresConfirmation()
                    // Staff already carry the gold check.
                    ->visible(fn (User $record): bool => auth()->user()?->isAdmin() && $record->accountBadge() === null)
                    ->action(function (User $record): void {
                        $record->grantVerification(auth()->user());

                        Notification::make()->title(__('shared.verification.verified_done'))->success()->send();
                    }),
                Action::make('unverify')
                    ->label(__('shared.verification.unverify'))
                    ->icon(Heroicon::XCircle)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => auth()->user()?->isAdmin() && $record->isVerifiedAccount())
                    ->action(function (User $record): void {
                        $record->revokeVerification(auth()->user());

                        Notification::make()->title(__('shared.verification.unverified_done'))->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
