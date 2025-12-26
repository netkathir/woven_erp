<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebitNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('debit_note_number')->unique();
            $table->date('debit_note_date');
            $table->enum('reference_document_type', ['Purchase Invoice', 'Sales Invoice', 'Dispatch', 'Manual'])->nullable();
            $table->string('reference_document_number')->nullable();
            $table->unsignedBigInteger('reference_document_id')->nullable(); // ID of the referenced document
            $table->enum('party_type', ['Supplier', 'Customer'])->nullable();
            $table->unsignedBigInteger('party_id')->nullable(); // Supplier or Customer ID
            $table->string('party_name')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('currency')->default('INR');
            $table->enum('debit_note_reason', ['Purchase Return', 'Rate Difference', 'Short Supply', 'Damage Compensation', 'Others'])->nullable();
            $table->text('remarks')->nullable();
            
            // Line item totals
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('cgst_amount', 15, 2)->default(0);
            $table->decimal('sgst_amount', 15, 2)->default(0);
            $table->decimal('igst_amount', 15, 2)->default(0);
            $table->decimal('adjustments', 15, 2)->default(0);
            $table->decimal('total_debit_amount', 15, 2)->default(0);
            
            // Status and workflow
            $table->enum('status', ['Draft', 'Submitted', 'Cancelled'])->default('Draft');
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->text('cancel_reason')->nullable();
            
            // Audit fields
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('debit_note_number');
            $table->index('reference_document_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debit_notes');
    }
}
