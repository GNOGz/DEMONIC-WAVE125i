<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_item_id',
        'user_id',
        'id',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function orderItems():HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_item_id', 'order_item_id');
    }


}
