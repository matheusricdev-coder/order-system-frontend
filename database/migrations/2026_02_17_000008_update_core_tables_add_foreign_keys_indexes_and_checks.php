<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id', 'orders_user_id_idx');
            $table->index('status', 'orders_status_idx');

            $table->foreign('user_id', 'orders_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id', 'order_items_order_id_idx');
            $table->index('product_id', 'order_items_product_id_idx');

            $table->foreign('order_id', 'order_items_order_id_fk')
                ->references('id')
                ->on('orders')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('product_id', 'order_items_product_id_fk')
                ->references('id')
                ->on('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->foreign('product_id', 'stocks_product_id_fk')
                ->references('id')
                ->on('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('company_id', 'users_company_id_idx');

            $table->foreign('company_id', 'users_company_id_fk')
                ->references('id')
                ->on('companies')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        $driver = DB::getDriverName();

        if ($driver !== 'sqlite') {
            DB::statement("ALTER TABLE stocks ADD CONSTRAINT stocks_quantity_total_non_negative_chk CHECK (quantity_total >= 0)");
            DB::statement("ALTER TABLE stocks ADD CONSTRAINT stocks_quantity_reserved_non_negative_chk CHECK (quantity_reserved >= 0)");
            DB::statement("ALTER TABLE order_items ADD CONSTRAINT order_items_quantity_positive_chk CHECK (quantity >= 1)");
            DB::statement("ALTER TABLE order_items ADD CONSTRAINT order_items_unit_price_amount_non_negative_chk CHECK (unit_price_amount >= 0)");
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_valid_chk CHECK (status IN ('created', 'paid', 'cancelled'))");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver !== 'sqlite') {
            DB::statement('ALTER TABLE orders DROP CHECK orders_status_valid_chk');
            DB::statement('ALTER TABLE order_items DROP CHECK order_items_unit_price_amount_non_negative_chk');
            DB::statement('ALTER TABLE order_items DROP CHECK order_items_quantity_positive_chk');
            DB::statement('ALTER TABLE stocks DROP CHECK stocks_quantity_reserved_non_negative_chk');
            DB::statement('ALTER TABLE stocks DROP CHECK stocks_quantity_total_non_negative_chk');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_company_id_fk');
            $table->dropIndex('users_company_id_idx');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign('stocks_product_id_fk');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign('order_items_product_id_fk');
            $table->dropForeign('order_items_order_id_fk');
            $table->dropIndex('order_items_product_id_idx');
            $table->dropIndex('order_items_order_id_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_user_id_fk');
            $table->dropIndex('orders_status_idx');
            $table->dropIndex('orders_user_id_idx');
        });
    }
};
