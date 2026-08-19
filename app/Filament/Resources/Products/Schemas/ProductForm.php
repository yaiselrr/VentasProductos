<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Nombre de la categoría')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('code')
                    ->label('Código')
                    ->placeholder('Ingrese el código')
                    ->required(),
                TextInput::make('name')
                    ->label('Nombre del producto')
                    ->placeholder('Ingrese el nombre del producto')
                    ->required(),
                RichEditor::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Imagen')
                    ->image(),
                TextInput::make('price_purchase')
                    ->label('Precio de compra')
                    ->placeholder('Ingrese el precio de compra')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('price_sale')
                    ->label('Precio de venta')
                    ->placeholder('Ingrese el Precio de venta')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('stock_min')
                    ->label('Stock mínimo')
                    ->placeholder('Ingrese el Stock mínimo')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('stock_max')
                    ->label('Stock máximo')
                    ->placeholder('Ingrese el stock máximo')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('state')
                    ->label('Estado')
                    ->required()
                    ->default('agotado')
                    ->options([
                        'agotado' => 'Agotado',
                        'disponible' => 'Disponible',
                    ]),
            ]);
    }
}
