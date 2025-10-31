<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'user_id',
        'product_id',
        'order_item_id',
    ];

    public function user():HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function orderItems():HasOne
    {
        return $this->hasOne(OrderItem::class, 'id', 'order_item_id');
    }


}
