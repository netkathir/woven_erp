<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_expense_id')->constrained()->cascadeOnDelete();
            $table->text('item_description');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('gst_percentage', 5, 2)->nullable();
            $table->decimal('gst_amount', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_expense_items');
    }
};

