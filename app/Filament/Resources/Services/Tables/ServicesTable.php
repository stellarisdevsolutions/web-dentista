<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Service;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'asc')
            ->recordClasses(fn (Service $record): string => "cursor-pointer service-row service-row-{$record->id}")
            ->recordAction(null)
            ->recordUrl(null)
            ->header(view('filament.resources.services.table-header'))
            ->emptyStateHeading('No hay tratamientos registrados')
            ->emptyStateDescription('Comienza añadiendo los servicios y procedimientos odontológicos de la clínica.')
            ->emptyStateIcon('heroicon-o-sparkles')
            ->columns([
                ImageColumn::make('image')
                    ->label('Imagen')
                    ->disk('public')
                    ->defaultImageUrl(fn (Service $record): string => $record->image_url)
                    ->extraImgAttributes(['class' => 'rounded-xl object-cover shadow-xs border border-slate-200/80'])
                    ->size(46),

                TextColumn::make('name')
                    ->label('Tratamiento')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Service $record): string => $record->short_description ? Str::limit($record->short_description, 50) : '—'),

                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Precio Base')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('duration_minutes')
                    ->label('Duración')
                    ->suffix(' min')
                    ->icon('heroicon-o-clock')
                    ->sortable(),

                TextColumn::make('dentists_count')
                    ->counts('dentists')
                    ->label('Especialistas')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-user-group')
                    ->sortable(),

                ToggleColumn::make('allows_online_booking')
                    ->label('Reserva Web')
                    ->afterStateUpdated(function (Service $record, bool $state): void {
                        Notification::make()
                            ->title($state ? 'Reserva online habilitada' : 'Reserva online pausada')
                            ->body($state ? "Los pacientes pueden reservar '{$record->name}' en la web." : "Se pausó la reserva online para '{$record->name}'.")
                            ->color($state ? 'success' : 'gray')
                            ->icon($state ? 'heroicon-o-globe-alt' : 'heroicon-o-pause-circle')
                            ->send();
                    }),

                ToggleColumn::make('is_featured')
                    ->label('Destacado')
                    ->afterStateUpdated(function (Service $record, bool $state): void {
                        Notification::make()
                            ->title($state ? 'Tratamiento destacado' : 'Tratamiento no destacado')
                            ->body($state ? "'{$record->name}' ahora se muestra en la portada web." : "'{$record->name}' se quitó de la portada web.")
                            ->color($state ? 'success' : 'gray')
                            ->icon($state ? 'heroicon-o-star' : 'heroicon-o-minus-circle')
                            ->send();
                    }),

                ToggleColumn::make('is_active')
                    ->label('Activo')
                    ->afterStateUpdated(function (Service $record, bool $state): void {
                        Notification::make()
                            ->title($state ? 'Tratamiento activado' : 'Tratamiento desactivado')
                            ->body($state ? "'{$record->name}' está activo en el catálogo y sistema." : "'{$record->name}' se desactivó del catálogo.")
                            ->color($state ? 'success' : 'gray')
                            ->icon($state ? 'heroicon-o-check-circle' : 'heroicon-o-pause-circle')
                            ->send();
                    }),

                TextColumn::make('order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado Activo'),
                TernaryFilter::make('allows_online_booking')
                    ->label('Reserva Online Permitida'),
                TernaryFilter::make('is_featured')
                    ->label('Destacado en Inicio'),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Editar tratamiento'),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Eliminar tratamiento'),
            ]);
    }
}
