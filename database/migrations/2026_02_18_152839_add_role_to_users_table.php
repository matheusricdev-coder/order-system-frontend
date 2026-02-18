<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // In a fresh migrate:fresh the create_users_table migration (which
        // already includes the role column) may not have run yet because it
        // sorts alphabetically after this timestamped file. Skip in that case —
        // the column will be created by create_users_table.
        if (!Schema::hasTable('users') || Schema::hasColumn('users', 'role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('active')
                ->comment('customer | admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
