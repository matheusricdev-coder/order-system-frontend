<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', static function (Blueprint $table): void {
            // Stripe PaymentIntent ID — set when payment is initiated.
            // Null for orders not yet in payment flow or cancelled before payment.
            $table->string('payment_intent_id')->nullable()->unique()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', static function (Blueprint $table): void {
            $table->dropColumn('payment_intent_id');
        });
    }
};
