<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedFieldsFromCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'payment_terms',
                'credit_limit',
                'notes',
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
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('payment_terms', ['cash', 'credit', 'advance', 'partial', 'other'])->default('credit')->after('gst_number');
            $table->decimal('credit_limit', 10, 2)->default(0)->after('payment_terms');
            $table->text('notes')->nullable()->after('credit_limit');
            $table->boolean('is_active')->default(true)->after('notes');
        });
    }
}
