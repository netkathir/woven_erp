<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->date('transaction_date');
            $table->enum('transaction_type', ['stock_in', 'stock_out']);
            $table->enum('item_type', ['raw_material', 'product']);
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 10, 2);
            $table->string('unit_of_measure');
            $table->enum('source_document_type', ['material_inward', 'sales_invoice'])->nullable();
            $table->unsignedBigInteger('source_document_id')->nullable();
            $table->string('source_document_number')->nullable();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};

