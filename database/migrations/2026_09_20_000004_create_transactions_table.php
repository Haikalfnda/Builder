<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->date('transaction_date');
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('tourism_place_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('income_source_id')->nullable()->constrained()->nullOnDelete();
            $table->string('package_name')->nullable();
            $table->string('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('payment_method');
            $table->string('status')->default('completed');
            $table->string('proof_path')->nullable();
            $table->timestamps();
            $table->index(['type', 'transaction_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('transactions'); }
};
