<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedFieldsFromEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['manager_id']);
            // Drop columns
            $table->dropColumn(['salary', 'manager_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('salary', 12, 2)->default(0)->after('department');
            $table->unsignedBigInteger('manager_id')->nullable()->after('joining_date');
            $table->boolean('is_active')->default(true)->after('manager_id');
            $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');
        });
    }
}
