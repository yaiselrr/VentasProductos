<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Purchase extends Model
{
    //
    protected $table = 'purchases';
    protected $fillable = [
        'provider_id',
        'date',
        'total',
        'state',
        'notes',
    ];

    public function purchaseDetails():BelongsToMany
    {
        return $this->belongsToMany(PurchaseDetail::class);
    }
    public function provider():BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
