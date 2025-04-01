<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('asaas_id')->nullable()->unique();
            $table->decimal('value', 10);
            $table->string('status');
            $table->string('billing_type');
            $table->date('due_date');
            $table->string('invoice_url')->nullable();
            $table->string('bank_slip_url')->nullable();
            $table->string('pix_qr_code')->nullable();
            $table->text('pix_copy_paste')->nullable();
            $table->json('asaas_data')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
