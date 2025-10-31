<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $fillable = [
        'quantity',
        'price',
        'product_id',
        'order_item_id'
    ];

    public function product():HasMany
    {
        return $this->hasMany(Product::class, 'product_id');
    }

    public function order(): BelongsTo
 {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
