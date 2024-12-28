<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    // 
    protected $table = 'order_items';

    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    public $timestamps = false;
}
