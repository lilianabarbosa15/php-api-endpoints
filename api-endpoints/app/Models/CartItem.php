<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CartItem extends Pivot
{
    // 
    protected $table = 'cart_items';
    
    /** @use HasFactory<\Database\Factories\CartItemFactory> */
    use HasFactory;

    public $timestamps = false;
}
