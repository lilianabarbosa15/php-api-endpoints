<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Models\CartItem;
use App\Models\User;

class ShoppingCart extends Model
{
    /** @use HasFactory<\Database\Factories\ShoppingCartFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function cart_items() {
        return $this->hasMany(CartItem::class, 'shopping_cart_id', 'id');
    }
}
