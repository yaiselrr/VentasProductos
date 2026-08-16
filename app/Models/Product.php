<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    //
    protected $table = 'products';
    protected $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'image',
        'price_purchase',
        'price_sale',
        'stock_min',
        'stock_max',
        'state'
    ];

    public function purchaseDetails():BelongsToMany
    {
        return $this->belongsToMany(PurchaseDetail::class);
    }
    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
