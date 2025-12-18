<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_id')->unique();
            $table->date('expense_date');
            $table->string('expense_category');
            $table->string('vendor_name');
            $table->enum('payment_method', ['cash', 'credit', 'bank_transfer']);
            $table->string('invoice_number')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('gst_applied', 5, 2)->nullable();
            $table->decimal('total_expense_amount', 15, 2)->default(0);
            $table->decimal('total_gst_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_expenses');
    }
};

