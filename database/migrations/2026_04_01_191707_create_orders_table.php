<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('status')->index();
            $table->string('customer_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('stripe_checkout_session_id')->nullable()->index();
            $table->decimal('total', 12, 2);
            $table->string('currency', 8)->default('usd');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('tickets_emailed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
