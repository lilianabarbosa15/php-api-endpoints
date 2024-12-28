<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Product;
use App\Models\Order;
use App\Models\ShoppingCart;

use App\Models\OrderItem;
use App\Models\CartItem;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory;

    public $timestamps = false;

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function orders() {
        return $this->belongsToMany(Order::class, 'order_items', 'product_variant_id', 'order_id')
                    ->withPivot('quantity', 'unit_price') // to access the pivot table columns
                    ->using(OrderItem::class);
    }

    public function shopping_carts() {
        return $this->belongsToMany(ShoppingCart::class, 'cart_items', 'product_variant_id', 'shopping_cart_id')
                    ->withPivot('quantity', 'unit_price') // to access the pivot table columns
                    ->using(CartItem::class);
    }
}
