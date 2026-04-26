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
        Schema::create('dpia_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dpia_id')->constrained('dpias')->onDelete('cascade');

            $table->string('risk_source');  // Es: Attacco hacker, Errore umano, Guasto hardware
            $table->string('potential_impact');  // Es: Perdita di riservatezza, danno reputazionale

            // Valutazione numerica (solitamente 1-4 o 1-5)
            $table->integer('probability');
            $table->integer('severity');  // Gravità dell'impatto

            // Rischio Inerente (calcolato via codice o stored procedure)
            $table->integer('inherent_risk_score');

            // Misura di mitigazione (collegata alla tabella security che abbiamo creato prima!)
            $table->foreignId('privacy_security_id')->nullable();

            // Rischio Residuo dopo la mitigazione
            $table->integer('residual_risk_score');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpia_items');
    }
};
