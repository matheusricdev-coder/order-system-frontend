<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_daily_logins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->date('date');
            $table->timestamps();

            $table->foreign('user_id', 'user_daily_logins_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->unique(['user_id', 'date'], 'user_daily_logins_user_date_unique');
            $table->index('user_id', 'user_daily_logins_user_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_daily_logins');
    }
};
