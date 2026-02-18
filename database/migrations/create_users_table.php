<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('surname');
            $table->date('birth_date');

            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique();

            $table->string('password');
            $table->string('cpf')->nullable();

            $table->boolean('active')->default(true);
            $table->uuid('company_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
