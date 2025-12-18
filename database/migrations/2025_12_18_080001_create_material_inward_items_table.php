<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('material_inward_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_inward_id')->constrained('material_inwards')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('quantity_received', 15, 3);
            $table->string('unit_of_measure');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_inward_items');
    }
};


