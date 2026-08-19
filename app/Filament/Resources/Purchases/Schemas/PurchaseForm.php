<?php

namespace App\Filament\Resources\Purchases\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use App\Http\Livewire\PurchaseProductManager;
use Filament\Schemas\Components\Livewire as ComponentsLivewire;

class PurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1) // Una sola columna
            ->components([
                // Sección 1: Información de la Compra
                Section::make('Información de la Compra')
                    ->collapsible() // Opcional: permite colapsar
                    ->collapsed(false) // Opcional: inicia abierto
                    ->schema([
                        Select::make('provider_id')
                            ->relationship('provider', 'name')
                            ->required()
                            ->searchable() // Para buscar proveedores
                            ->preload(),
                        DateTimePicker::make('date')
                            ->required()
                            ->default(now()),
                        // TextInput::make('state')
                        //     ->required()
                        //     ->default('pendiente'),
                        Select::make('state')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'completada' => 'Completada',
                                'cancelada' => 'Cancelada',
                            ]),
                        Textarea::make('notes')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),

                // Sección 2: Productos
                Section::make('Productos')
                    ->collapsible()
                    ->collapsed(false)
                    ->visible(fn($livewire) => $livewire->getRecord() !== null)
                    ->schema([
                        ComponentsLivewire::make(PurchaseProductManager::class, [
                            'purchaseId' => $schema->getRecord()?->id
                        ])
                    ]),
            ]);
    }
}
