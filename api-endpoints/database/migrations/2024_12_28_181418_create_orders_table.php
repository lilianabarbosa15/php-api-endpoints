<?php

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            //UserID
            //OrderDate
            $table->integer('total_amount')
                ->unsigned()
                ->default(0);
            $table->enum('order_status', ['pending', 'completed', 'shipped', 'cancelled'])
                ->default('pending');
            $table->string('payment_method'); // Visa, MasterCard, Discover, UnionPay, Diners Club, JCB, AMEX.
            $table->text('shipping_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
