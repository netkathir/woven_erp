<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedFieldsFromRawMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['supplier_id']);
            // Drop columns
            $table->dropColumn([
                'quantity_available',
                'supplier_id',
                'price_per_unit',
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
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->decimal('quantity_available', 10, 2)->default(0)->after('unit_of_measure');
            $table->unsignedBigInteger('supplier_id')->nullable()->after('reorder_level');
            $table->decimal('price_per_unit', 10, 2)->default(0)->after('supplier_id');
            $table->decimal('gst_percentage', 5, 2)->default(0)->after('price_per_unit');
            $table->text('description')->nullable()->after('gst_percentage');
            $table->boolean('is_active')->default(true)->after('description');
            
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }
}
