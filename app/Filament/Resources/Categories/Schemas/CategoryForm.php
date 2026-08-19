<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Nombre de la categoría')
                    ->placeholder('Ingrese el nombre de la categoría'),
                RichEditor::make('description')
                    ->label('Descripción de la categoría')
                    ->columnSpanFull(),
            ]);
    }
}
