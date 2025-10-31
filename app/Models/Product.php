<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'in_stock',
        'description',
        'image_url',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
   

    public function carts()
    {
        return $this->hasMany(Cart::class, 'product_id');
    }

    public function hasAvailableStock($requestedQuantity = 1)
    {
        return $this->in_stock >= $requestedQuantity;
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}
