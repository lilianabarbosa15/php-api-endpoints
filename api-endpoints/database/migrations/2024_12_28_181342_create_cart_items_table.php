<?php

use App\Models\ProductVariant;
use App\Models\ShoppingCart;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();                                   //CartItemID
            $table->foreignIdFor(ShoppingCart::class)       //CartID
                ->nullable()
                ->constrained();
            $table->foreignIdFor(ProductVariant::class)     //VariantID
                ->nullable()
                ->constrained();
            /*$table->integer('quantity')
                ->unsigned()
                ->default(0);
            $table->integer('unit_price')
                ->unsigned()
                ->default(0);*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
