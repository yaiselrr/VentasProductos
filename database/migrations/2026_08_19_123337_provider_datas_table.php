<?php

use App\Models\Provider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('providers')) {
            Provider::firstOrCreate(
                [
                    'company' => 'Papas & Comany',
                    'name' => 'Juan Segura Pérez',
                    'phone' => '52585957',
                    'email' => 'juansegura@gmail.com',
                    'address' => 'Dirección del proveedor',
                    'description' => 'Descripción del proveedor',
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            Provider::firstOrCreate(
                [
                    'company' => 'EMBER',
                    'name' => 'Mario Mendoza Torres',
                    'phone' => '54585957',
                    'email' => 'mariomendoza@gmail.com',
                    'address' => 'Dirección del proveedor',
                    'description' => 'Descripción del proveedor',
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            Provider::firstOrCreate(
                [
                    'company' => 'Calzados SA',
                    'name' => 'Emma Viena del Catillo',
                    'phone' => '59585957',
                    'email' => 'emmav@gmail.com',
                    'address' => 'Dirección del proveedor',
                    'description' => 'Descripción del proveedor',
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            Provider::firstOrCreate(
                [
                    'company' => 'Suchel Camacho',
                    'name' => 'Felipe Camacho Antunes',
                    'phone' => '53585957',
                    'email' => 'felipecamacho@gmail.com',
                    'address' => 'Dirección del proveedor',
                    'description' => 'Descripción del proveedor',
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            Provider::firstOrCreate(
                [
                    'company' => 'Salón Rojo',
                    'name' => 'Juana Valdes Valdes',
                    'phone' => '51585957',
                    'email' => 'juanavaldes@gmail.com',
                    'address' => 'Dirección del proveedor',
                    'description' => 'Descripción del proveedor',
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('providers')->truncate();
    }
};
