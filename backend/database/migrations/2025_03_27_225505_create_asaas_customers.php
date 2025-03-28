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
        Schema::create('asaas_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('asaas_id')->unique();
            $table->date('date_created')->nullable();
            $table->string('external_reference')->nullable();
            $table->boolean('notification_disabled')->default(false);
            $table->text('observations')->nullable();
            $table->boolean('foreign_customer')->default(false);
            $table->string('additional_emails')->nullable();
            $table->string('person_type')->nullable();
            $table->boolean('deleted')->default(false);
            $table->json('asaas_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asaas_customers');
    }
};
