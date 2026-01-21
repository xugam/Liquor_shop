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
        Schema::create('location_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('product_units')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->default(0); // Current stock in base units
            $table->decimal('reorder_level', 10, 2)->default(0); // For low-stock alerts
            $table->timestamps();

            // Ensure one record per product per location
            $table->unique(['unit_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_products');
    }
};
