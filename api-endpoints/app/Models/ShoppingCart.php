<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ProductVariant;
use App\Models\CartItem;
use App\Models\User;

class ShoppingCart extends Model
{
    /** @use HasFactory<\Database\Factories\ShoppingCartFactory> */
    use HasFactory;

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function product_variants() {
        return $this->belongsToMany(ProductVariant::class, 'cart_items', 'shopping_cart_id', 'product_variant_id')
                    ->withPivot('quantity', 'unit_price') // to access the pivot table columns
                    ->using(CartItem::class);
    }
}
