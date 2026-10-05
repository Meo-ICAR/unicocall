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
        Schema::connection('mariadb')->create('registro_trattamentis', function (Blueprint $table) {
            $table->id();
            $table->uuid('company_id')->comment('Company UUID reference');
            $table->string('name')->comment('Name of the treatment register');
            $table->dateTime('approved_at')->nullable()->comment('When the register was approved');
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('company_id');
            $table->index('approved_at');

            // Note: Foreign key to companies table in mysql_proforma database
            // Cannot create cross-database foreign key constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mariadb')->dropIfExists('registro_trattamenti');
    }
};
