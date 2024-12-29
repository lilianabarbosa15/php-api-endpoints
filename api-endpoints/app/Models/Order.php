<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ProductVariant;
use App\Models\OrderItem;
use App\Models\User;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product_variants() {
        return $this->belongsToMany(ProductVariant::class, 'order_items', 'order_id', 'product_variant_id')
                    ->withPivot('quantity', 'unit_price') // to access the pivot table columns
                    ->using(OrderItem::class);
    }
}
