<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mariadb')->create('registro_trattamenti_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('company_id')->comment('Company UUID reference');
            $table->text('Attivita')->comment('Treatment activities description');
            $table->text('Finalita')->comment('Purpose of the treatment');
            $table->text('Interessati')->comment('Data subjects involved');
            $table->text('Dati')->comment('Categories of data processed');
            $table->text('Giuridica')->comment('Legal basis for processing');
            $table->text('Destinatari')->comment('Data recipients');
            $table->boolean('extraEU')->default(false)->comment('Data transfer outside EU');
            $table->text('Conservazione')->comment('Data retention period');
            $table->text('Sicurezza')->comment('Security measures implemented');
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('company_id');
            $table->index('extraEU');
            $table->index(['company_id', 'extraEU']);

            // Note: Foreign key to companies table in mysql_proforma database
            // Cannot create cross-database foreign key constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mariadb')->dropIfExists('registro_trattamenti_items');
    }
};
