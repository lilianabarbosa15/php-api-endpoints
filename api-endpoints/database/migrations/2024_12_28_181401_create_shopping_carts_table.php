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
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();                       //CartID
            $table->foreignIdFor(User::class)   // UserID
                ->constrained()
                ->onDelete('cascade');
            $table->enum('status', ['pending', 'completed', 'shipped', 'cancelled'])
                ->default('pending');
            $table->timestamps();   //OrderDate (created_at), LastUpdate (updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_carts');
    }
};
