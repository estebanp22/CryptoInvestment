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
        Schema::create('cryptocurrency_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cryptocurrency_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 18, 8);
            $table->decimal('percent_change_1h', 10, 4);
            $table->decimal('percent_change_24h', 10, 4);
            $table->decimal('percent_change_7d', 10, 4);
            $table->decimal('volume_24h', 18, 2);
            $table->decimal('market_cap', 18, 2);
            $table->timestamp('recorded_at');
            $table->timestamp('timestamp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cryptocurrency_prices');
    }
};
