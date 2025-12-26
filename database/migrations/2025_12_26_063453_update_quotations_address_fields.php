<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run if table exists and has old columns
        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                // Add new columns if they don't exist
                if (!Schema::hasColumn('quotations', 'address_line_2')) {
                    $table->string('address_line_2')->nullable()->after('address_line_1');
                }
                if (!Schema::hasColumn('quotations', 'state')) {
                    $table->string('state')->nullable()->after('city');
                }
                if (!Schema::hasColumn('quotations', 'country')) {
                    $table->string('country')->default('India')->after('state');
                }
            });

            // Rename columns if they exist (using raw SQL for better compatibility)
            if (Schema::hasColumn('quotations', 'zip_code') && !Schema::hasColumn('quotations', 'postal_code')) {
                DB::statement('ALTER TABLE quotations CHANGE zip_code postal_code VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('quotations', 'street_address') && !Schema::hasColumn('quotations', 'address_line_1')) {
                DB::statement('ALTER TABLE quotations CHANGE street_address address_line_1 VARCHAR(255) NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('quotations')) {
            Schema::table('quotations', function (Blueprint $table) {
                if (Schema::hasColumn('quotations', 'address_line_2')) {
                    $table->dropColumn('address_line_2');
                }
                if (Schema::hasColumn('quotations', 'state')) {
                    $table->dropColumn('state');
                }
                if (Schema::hasColumn('quotations', 'country')) {
                    $table->dropColumn('country');
                }
            });

            if (Schema::hasColumn('quotations', 'postal_code') && !Schema::hasColumn('quotations', 'zip_code')) {
                DB::statement('ALTER TABLE quotations CHANGE postal_code zip_code VARCHAR(255) NULL');
            }
            if (Schema::hasColumn('quotations', 'address_line_1') && !Schema::hasColumn('quotations', 'street_address')) {
                DB::statement('ALTER TABLE quotations CHANGE address_line_1 street_address VARCHAR(255) NULL');
            }
        }
    }
};
