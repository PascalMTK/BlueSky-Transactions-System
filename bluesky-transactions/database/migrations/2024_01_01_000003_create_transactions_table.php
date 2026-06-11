<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 30)->unique();
            $table->string('sender_name');
            $table->string('sender_phone', 25);
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone', 25)->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('fee_percentage', 5, 2)->default(3.00);
            $table->decimal('fee_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->foreignId('origin_country_id')->constrained('countries');
            $table->foreignId('destination_country_id')->constrained('countries');
            $table->foreignId('agent_id')->constrained('users');
            $table->string('status')->default('completed'); // pending, completed, cancelled
            $table->text('notes')->nullable();
            $table->string('payment_method')->default('cash'); // cash, mobile_money, bank
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
