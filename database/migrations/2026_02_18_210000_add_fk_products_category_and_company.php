<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id', 'products_category_id_idx');

            $table->foreign('category_id', 'products_category_id_fk')
                ->references('id')
                ->on('categories')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index('company_id', 'products_company_id_idx');

            $table->foreign('company_id', 'products_company_id_fk')
                ->references('id')
                ->on('companies')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_company_id_fk');
            $table->dropIndex('products_company_id_idx');
            $table->dropForeign('products_category_id_fk');
            $table->dropIndex('products_category_id_idx');
        });
    }
};
