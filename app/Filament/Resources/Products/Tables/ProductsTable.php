<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable(),
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                ImageColumn::make('image')->label('Imagen'),
                TextColumn::make('price_purchase')
                    ->label('Precio Compra')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_sale')
                    ->label('Precio Venta')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock_min')
                    ->label('Stock Mínimo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock_max')
                    ->label('Stock Máximo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('state')
                    ->label('Estado')
                    ->searchable(),
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
                //
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
