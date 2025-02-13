<?php

use App\Models\Product;
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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();                           //VariantID
            $table->foreignIdFor(Product::class)    //ProductID
                ->constrained()
                ->onDelete('cascade');
            $table->string('color', 7);             //#RRGGBB
            $table->string('size', 3);              // Size as a string (S, M, L, XL, etc.)
            $table->integer('stock_quantity')
                ->unsigned()
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
