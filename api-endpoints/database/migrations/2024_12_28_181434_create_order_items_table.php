<?php

use App\Models\Order;
use App\Models\ProductVariant;
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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();                           //OrderItemID
            $table->foreignIdFor(Order::class)      //OrderID
                    ->constrained()
                    ->onDelete('cascade');
            $table->foreignIdFor(ProductVariant::class) //VariantID
                    ->constrained()
                    ->onDelete('cascade');
            $table->integer('quantity')
                ->unsigned()
                ->default(0);
            $table->decimal('unit_price',5,2)
                ->unsigned()
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
