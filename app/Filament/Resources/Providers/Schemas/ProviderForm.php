<?php

namespace App\Filament\Resources\Providers\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company')
                    ->label('Empresa')
                    ->placeholder('Ingrese la empresa')
                    ->required(),
                TextInput::make('name')
                    ->label('Nombre')
                    ->placeholder('Ingrese el nombre del proveedor')
                    ->required(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->placeholder('Ingrese el Teléfono')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->placeholder('Ingrese el correo electrónico')
                    ->label('Email address')
                    ->email()
                    ->required(),
                RichEditor::make('address')
                    ->label('Dirección')
                    ->columnSpanFull(),
                RichEditor::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
            ]);
    }
}
