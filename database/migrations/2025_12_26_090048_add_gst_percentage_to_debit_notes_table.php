<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGstPercentageToDebitNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->decimal('gst_percentage', 5, 2)->default(18)->after('gst_classification');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->dropColumn('gst_percentage');
        });
    }
}
