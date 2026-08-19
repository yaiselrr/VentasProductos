<?php

use App\Models\Category;
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
        if (Schema::hasTable('categories')) {
            $categories = [
                'Alimentos',
                'Ferreteria',
                'Perfumeria',
                'Peleteria'
            ];

            foreach ($categories as $categoryname) {
                Category::firstOrCreate(
                    [
                        'name' => $categoryname,
                        'description' => 'Esta es la descripción de esta categoría ' . $categoryname
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->truncate();
    }
};
