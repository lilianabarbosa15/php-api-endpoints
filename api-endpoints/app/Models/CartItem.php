<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    // 
    public $timestamps = false;

    /** @use HasFactory<\Database\Factories\CartItemFactory> */
    use HasFactory;

    protected $fillable = [
        'shopping_cart_id',
        'product_variant_id',
        'quantity',
        'unit_price',
    ];

    public function shopping_cart()
    {
        return $this->belongsTo(Order::class, 'shopping_cart_id', 'id');
    }

    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id', 'id');
    }

}
