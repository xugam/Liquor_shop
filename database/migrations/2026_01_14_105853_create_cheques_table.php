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
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('cheque_number')->unique();
            $table->string('bank_name');
            $table->decimal('amount', 10, 2);
            $table->date('cheque_date');
            $table->date('cashable_date');
            $table->date('reminder_date');
            $table->string('phone_no');
            $table->enum('status', ['pending', 'deposited', 'cleared', 'bounced'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
