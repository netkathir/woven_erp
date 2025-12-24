<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedFieldsFromProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'product_category',
                'price_per_unit',
                'stock_quantity',
                'gst_percentage',
                'description',
                'is_active'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_category')->after('unit_of_measure');
            $table->decimal('price_per_unit', 10, 2)->default(0)->after('product_category');
            $table->decimal('stock_quantity', 10, 2)->default(0)->after('price_per_unit');
            $table->decimal('gst_percentage', 5, 2)->default(0)->after('stock_quantity');
            $table->text('description')->nullable()->after('gst_percentage');
            $table->boolean('is_active')->default(true)->after('description');
        });
    }
}
