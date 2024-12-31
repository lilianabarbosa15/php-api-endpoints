<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Models\OrderItem;
use App\Models\User;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'order_status',
        'payment_method',
        'shipping_address',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function order_items() {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
}
