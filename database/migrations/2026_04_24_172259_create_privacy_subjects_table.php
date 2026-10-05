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
        Schema::create('privacy_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Es: Prospect, Clienti, Dipendenti
            $table->string('industry_sector');  // Call Center, Software House, ecc.
            $table->text('description')->nullable();

            // Origine del dato (Fondamentale per List Provider e Mediatori)
            $table
                ->enum('data_source', ['direct', 'third_party', 'public_records', 'mixed'])
                ->default('direct');

            // Flag per categorie protette (Minori, soggetti vulnerabili)
            $table->boolean('has_vulnerable_subjects')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privacy_subjects');
    }
};
