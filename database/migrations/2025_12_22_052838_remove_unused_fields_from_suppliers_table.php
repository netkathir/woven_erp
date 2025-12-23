<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedFieldsFromSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['payment_terms', 'bank_details', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->enum('payment_terms', ['cash', 'credit', 'advance', 'partial', 'other'])->default('credit')->after('country');
            $table->text('bank_details')->nullable()->after('gst_number');
            $table->boolean('is_active')->default(true)->after('bank_details');
        });
    }
}
