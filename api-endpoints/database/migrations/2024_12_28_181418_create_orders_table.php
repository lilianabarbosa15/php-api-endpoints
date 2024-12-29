<?php

use App\Models\User;
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
            $table->id();                       //OrderID
            $table->foreignIdFor(User::class)   // UserID
                ->constrained()
                ->cascadeOnDelete();
            $table->integer('total_amount')
                ->unsigned()
                ->default(0);
            $table->enum('order_status', ['pending', 'completed', 'shipped', 'cancelled'])
                ->default('pending');
            $table->string('payment_method'); // Visa, MasterCard, Discover, UnionPay, Diners Club, JCB, AMEX.
            $table->text('shipping_address');
            $table->timestamps();   //OrderDate (created_at), LastUpdate (updated_at)
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
