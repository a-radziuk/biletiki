<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'swipe_hash_id')) {
                $table->dropColumn(['swipe_hash_id', 'swipe_serial_number']);
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'stripe_checkout_session_id')) {
                $table->string('stripe_checkout_session_id')->nullable()->after('phone')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'stripe_checkout_session_id')) {
                $table->dropColumn('stripe_checkout_session_id');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'swipe_hash_id')) {
                $table->string('swipe_hash_id')->nullable()->index();
                $table->string('swipe_serial_number')->nullable();
            }
        });
    }
};
