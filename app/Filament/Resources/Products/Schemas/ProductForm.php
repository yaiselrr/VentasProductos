<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
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
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image(),
                TextInput::make('price_purchase')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('price_sale')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('stock_min')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('stock_max')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('state')
                    ->required()
                    ->default('agotado'),
            ]);
    }
}
