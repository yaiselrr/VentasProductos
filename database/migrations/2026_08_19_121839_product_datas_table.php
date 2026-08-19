<?php

use App\Models\Category;
use App\Models\Product;
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
        if (Schema::hasTable('products')) {
            $categories = Category::all();

            foreach ($categories as $category) {
                switch ($category->name) {
                    case 'Alimentos':
                        Product::firstOrCreate(
                            [
                                'category_id' => $category->id,
                                'code' => '0001',
                                'name' => 'Pollo',
                                'description' => 'Descripción de las cajas de pollos',
                                'price_purchase' => 100,
                                'price_sale' => 150,
                                'stock_min' => 1,
                                'stock_max' => 10000,
                                'state' => 'disponible'
                            ],
                            [
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        );
                        break;
                    case 'Ferreteria':
                        Product::firstOrCreate(
                            [
                                'category_id' => $category->id,
                                'code' => '0002',
                                'name' => 'Fregaderos',
                                'description' => 'Descripción de los fregaderos',
                                'price_purchase' => 1000,
                                'price_sale' => 1500,
                                'stock_min' => 1,
                                'stock_max' => 10000,
                                'state' => 'disponible'
                            ],
                            [
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        );
                        break;
                    case 'Perfumeria':
                        Product::firstOrCreate(
                            [
                                'category_id' => $category->id,
                                'code' => '0003',
                                'name' => 'Colonia de bebito',
                                'description' => 'Descripción de la colonia de bebito',
                                'price_purchase' => 10,
                                'price_sale' => 15,
                                'stock_min' => 1,
                                'stock_max' => 10000,
                                'state' => 'disponible'
                            ],
                            [
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        );
                        break;
                    case 'Peleteria':
                        Product::firstOrCreate(
                            [
                                'category_id' => $category->id,
                                'code' => '0004',
                                'name' => 'Tennis deportivos',
                                'description' => 'Descripción de los tennis deportivos',
                                'price_purchase' => 2000,
                                'price_sale' => 3500,
                                'stock_min' => 1,
                                'stock_max' => 10000,
                                'state' => 'disponible'
                            ],
                            [
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        );
                        break;

                    default:
                        # code...
                        break;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('products')->truncate();
    }
};
